#!/usr/bin/env node
/**
 * Package a production-ready zip of the Earthbound theme.
 *
 * Produces `earthbound.zip` containing a single top-level `earthbound/`
 * folder, ready to upload via Appearance → Themes → Add New → Upload Theme.
 *
 * Only the files the theme needs at runtime are included. Dev tooling,
 * local config, AI notes, and demo content (featured images, README
 * screenshots) are left out. The allowlist below is the single source of
 * truth — add a path here when you ship a new production file.
 *
 * Usage: npm run build
 */

import { createWriteStream } from 'node:fs';
import { readdir, readFile, stat } from 'node:fs/promises';
import { dirname, join, posix, relative, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import archiver from 'archiver';

const THEME_ROOT = resolve( dirname( fileURLToPath( import.meta.url ) ), '..' );
const THEME_SLUG = 'earthbound';
const OUTPUT = join( THEME_ROOT, `${ THEME_SLUG }.zip` );

/**
 * Paths (relative to the theme root) that ship to production.
 * A directory pulls in everything beneath it recursively.
 */
const INCLUDE = [
	'style.css', // Theme header + base styles (required).
	'theme.json', // Design system tokens (required).
	'functions.php', // Theme bootstrap.
	'readme.txt', // WordPress.org-style readme.
	'inc', // PHP includes (CPT, REST endpoints, customizer, caching).
	'templates', // Block templates.
	'parts', // Template parts (header, footer, sidebar).
	'patterns', // Block patterns.
	'styles', // Style variations.
	'blocks', // Custom blocks (block.json, render.php, view.js, style.css).
	'assets/css', // Enqueued block + editor styles.
	'assets/js', // Customizer preview script.
	'assets/fonts', // Self-hosted WOFF2 faces referenced by theme.json.
];

/** Never bundle these, even if they live under an included directory. */
const EXCLUDE_NAMES = new Set( [ '.DS_Store', 'Thumbs.db' ] );

/**
 * Recursively collect the files under an included path.
 *
 * @param {string} rel Path relative to the theme root.
 * @return {Promise<string[]>} Sorted list of file paths relative to the root.
 */
async function collect( rel ) {
	// `rel` is always POSIX-separated (see INCLUDE and the recursion below);
	// native join() accepts forward slashes, so filesystem access stays correct
	// cross-platform while the stored archive name remains POSIX.
	const abs = join( THEME_ROOT, rel );
	let info;
	try {
		info = await stat( abs );
	} catch {
		throw new Error( `Listed in INCLUDE but missing on disk: ${ rel }` );
	}

	if ( info.isFile() ) {
		return EXCLUDE_NAMES.has( rel.split( '/' ).pop() ) ? [] : [ rel ];
	}

	const entries = await readdir( abs, { withFileTypes: true } );
	const files = [];
	for ( const entry of entries ) {
		if ( EXCLUDE_NAMES.has( entry.name ) ) {
			continue;
		}
		// Build child paths with POSIX separators so ZIP entry names use `/`
		// on every platform (the ZIP format requires forward slashes).
		files.push( ...( await collect( posix.join( rel, entry.name ) ) ) );
	}
	return files;
}

/**
 * Read the theme version from the style.css header, for the build summary.
 *
 * @return {Promise<string>} The version string, or 'unknown'.
 */
async function themeVersion() {
	const header = await readFile( join( THEME_ROOT, 'style.css' ), 'utf8' );
	return header.match( /^\s*Version:\s*(.+)$/m )?.[ 1 ].trim() ?? 'unknown';
}

const files = ( await Promise.all( INCLUDE.map( collect ) ) ).flat().sort();

const output = createWriteStream( OUTPUT );
const archive = archiver( 'zip', { zlib: { level: 9 } } );

const done = new Promise( ( resolvePromise, rejectPromise ) => {
	output.on( 'close', resolvePromise );
	output.on( 'error', rejectPromise ); // Surface write failures (disk full, permissions).
	archive.on( 'warning', rejectPromise );
	archive.on( 'error', rejectPromise );
} );

archive.pipe( output );
for ( const file of files ) {
	// Prefix every entry with `earthbound/` so the zip extracts into the
	// correctly-named theme directory.
	archive.file( join( THEME_ROOT, file ), { name: `${ THEME_SLUG }/${ file }` } );
}
await archive.finalize();
await done;

const version = await themeVersion();
const kb = ( archive.pointer() / 1024 ).toFixed( 1 );
process.stdout.write(
	`\nPackaged ${ THEME_SLUG } v${ version } → ${ relative( process.cwd(), OUTPUT ) }\n` +
		`  ${ files.length } files, ${ kb } KB compressed\n`
);
