import './bootstrap';

// Mount the React frontend (located in project `src`) when Blade loads `resources/js/app.js` via Vite
try {
	import('../../../src/Components/main.jsx');
} catch (e) {
	// If the path isn't available in this environment, it's okay — the standalone Vite dev server can load the React entry.
}
