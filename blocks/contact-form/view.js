/**
 * Contact Form - Interactivity API module.
 *
 * Validates on the client for fast feedback, then posts to the theme's REST
 * endpoint. The endpoint revalidates everything and owns the recipient address,
 * which is never sent to the browser.
 *
 * @package Earthbound
 */

import * as interactivity from '@wordpress/interactivity';

const { store, getContext, getElement } = interactivity;

/**
 * withSyncEvent() arrived in WordPress 6.9, and from 7.0 it is required for any
 * action that calls a synchronous event method such as preventDefault().
 *
 * It is read off a namespace import rather than named directly, because a named
 * import of a missing export fails at module resolution, and the theme still
 * supports 6.7 and 6.8. Those releases ran handlers synchronously regardless,
 * so passing the handler straight through is the correct behaviour there.
 */
const withSyncEvent = interactivity.withSyncEvent ?? ( ( handler ) => handler );

/**
 * Minimal email shape check. The server is the authority via is_email().
 */
const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

const { state, actions, callbacks } = store( 'earthbound/contact-form', {
	state: {
		get hasNameError() {
			return getContext().errors.name !== '';
		},
		get hasEmailError() {
			return getContext().errors.email !== '';
		},
		get hasMessageError() {
			return getContext().errors.message !== '';
		},
		get hasFormError() {
			return getContext().formError !== '';
		},
		get isSent() {
			return getContext().isSent;
		},
		get buttonText() {
			const context = getContext();
			return context.isSubmitting ? 'Sending…' : context.submitLabel;
		},
		get statusMessage() {
			// Only the in-flight state. The confirmation announces itself through
			// role="status" and the form-level failure through role="alert", so
			// repeating either here would announce it twice.
			return getContext().isSubmitting ? 'Sending your message.' : '';
		},
	},
	actions: {
		updateName( event ) {
			const context = getContext();
			context.name = event.target.value;
			context.errors.name = '';
		},
		updateEmail( event ) {
			const context = getContext();
			context.email = event.target.value;
			context.errors.email = '';
		},
		updateMessage( event ) {
			const context = getContext();
			context.message = event.target.value;
			context.errors.message = '';
		},
		updateWebsite( event ) {
			getContext().website = event.target.value;
		},
		submit: withSyncEvent( function* ( event ) {
			event.preventDefault();

			const context = getContext();
			if ( context.isSubmitting ) {
				return;
			}

			context.formError = '';
			context.errors = callbacks.validate( context );

			// Resolved here because element scope is not available inside a
			// requestAnimationFrame callback later on.
			const root = callbacks.getRoot();

			const firstInvalid = Object.keys( context.errors ).find(
				( field ) => context.errors[ field ] !== ''
			);

			if ( firstInvalid ) {
				callbacks.focusField( root, firstInvalid );
				return;
			}

			context.isSubmitting = true;

			try {
				const response = yield fetch( context.restUrl, {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify( {
						name: context.name,
						email: context.email,
						message: context.message,
						website: context.website,
						elapsed: Date.now() - context.loadedAt,
					} ),
				} );

				const payload = yield response.json();

				if ( ! response.ok ) {
					// Field-level errors come back under data.fields.
					const fields = payload?.data?.fields;
					if ( fields ) {
						context.errors = {
							name: fields.name || '',
							email: fields.email || '',
							message: fields.message || '',
						};
						const firstServerError = Object.keys( context.errors ).find(
							( field ) => context.errors[ field ] !== ''
						);
						if ( firstServerError ) {
							callbacks.focusField( root, firstServerError );
						}
					}

					context.formError =
						payload?.message ||
						'The message could not be sent. Please try again later.';
					return;
				}

				context.successMessage = payload.message;
				context.isSent = true;
				callbacks.focusSuccess( root );
			} catch ( error ) {
				context.formError =
					'The message could not be sent. Please check your connection and try again.';
			} finally {
				context.isSubmitting = false;
			}
		} ),
	},
	callbacks: {
		init() {
			// Used to reject submissions completed faster than a person could type.
			getContext().loadedAt = Date.now();
		},
		validate( context ) {
			const errors = { name: '', email: '', message: '' };

			if ( context.name.trim() === '' ) {
				errors.name = 'Please enter your name.';
			} else if ( context.name.trim().length > 100 ) {
				errors.name = 'Please keep your name under 100 characters.';
			}

			if ( context.email.trim() === '' ) {
				errors.email = 'Please enter your email address.';
			} else if ( ! EMAIL_PATTERN.test( context.email.trim() ) ) {
				errors.email = 'Please enter a valid email address.';
			}

			if ( context.message.trim() === '' ) {
				errors.message = 'Please enter a message.';
			} else if ( context.message.trim().length > 5000 ) {
				errors.message = 'Please keep your message under 5000 characters.';
			}

			return errors;
		},
		getRoot() {
			const { ref } = getElement();
			// The submit handler sits on the form; the confirmation is its sibling,
			// so resolve the block wrapper and search from there.
			return ref.closest( '.contact-form' ) || ref;
		},
		focusField( root, field ) {
			const input = root.querySelector( `[name="${ field }"]` );
			if ( input ) {
				input.focus();
			}
		},
		focusSuccess( root ) {
			// Focus lands on the confirmation once the form has been hidden.
			window.requestAnimationFrame( () => {
				const success = root.querySelector( '.contact-form__success' );
				if ( success ) {
					success.focus();
				}
			} );
		},
	},
} );
