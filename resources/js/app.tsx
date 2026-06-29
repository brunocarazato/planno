import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import type { ComponentType } from 'react';
import { createRoot } from 'react-dom/client';

type PageModule = {
    default: ComponentType;
};

const pages = import.meta.glob<PageModule>('./pages/**/*.tsx', { eager: true });

createInertiaApp({
    title: (title) => (title ? `${title} - Planno` : 'Planno'),
    defaults: {
        visitOptions: (_href, options) => ({
            ...options,
            viewTransition: !options.method || options.method === 'get',
        }),
    },
    resolve: (name) => {
        const page = pages[`./pages/${name}.tsx`];

        if (!page) {
            throw new Error(`Página Inertia não encontrada: ${name}`);
        }

        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: '#0e7490',
    },
});
