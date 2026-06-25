import * as React from 'react';
import { cva, type VariantProps } from 'class-variance-authority';

import { cn } from '../lib/utils';

const buttonVariants = cva(
    'inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:pointer-events-none disabled:opacity-50',
    {
        variants: {
            variant: {
                default: 'bg-cyan-700 text-white hover:bg-cyan-800 focus-visible:outline-cyan-700',
                secondary: 'border border-slate-300 bg-white text-slate-900 hover:bg-slate-50 focus-visible:outline-slate-500',
                ghost: 'text-slate-700 hover:bg-slate-100 focus-visible:outline-slate-500',
            },
            size: {
                default: 'h-10 px-4 py-2',
                sm: 'h-9 px-3',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

export interface ButtonProps
    extends React.ButtonHTMLAttributes<HTMLButtonElement>,
        VariantProps<typeof buttonVariants> {}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
    ({ className, variant, size, ...props }, ref) => (
        <button className={cn(buttonVariants({ variant, size, className }))} ref={ref} {...props} />
    ),
);

Button.displayName = 'Button';

export { Button, buttonVariants };
