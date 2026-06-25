import { PropsWithChildren, useEffect, useRef } from 'react';

import { cn } from '../lib/utils';

type DialogProps = PropsWithChildren<{
    aberto: boolean;
    descricao?: string;
    onClose: () => void;
    titulo: string;
    className?: string;
}>;

export function Dialog({ aberto, children, className, descricao, onClose, titulo }: DialogProps) {
    const dialogRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!aberto) {
            return;
        }

        dialogRef.current?.focus();

        function handleKeyDown(event: KeyboardEvent) {
            if (event.key === 'Escape') {
                onClose();
            }
        }

        document.addEventListener('keydown', handleKeyDown);

        return () => document.removeEventListener('keydown', handleKeyDown);
    }, [aberto, onClose]);

    if (!aberto) {
        return null;
    }

    return (
        <div
            aria-modal="true"
            className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/45 px-4 py-6"
            onClick={onClose}
            role="dialog"
        >
            <div
                className={cn(
                    'w-full max-w-lg rounded-lg border border-slate-200 bg-white p-6 shadow-xl outline-none',
                    className,
                )}
                onClick={(event) => event.stopPropagation()}
                ref={dialogRef}
                tabIndex={-1}
            >
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <h2 className="text-lg font-semibold text-slate-950">{titulo}</h2>
                        {descricao ? <p className="mt-2 text-sm leading-6 text-slate-600">{descricao}</p> : null}
                    </div>
                    <button
                        aria-label="Fechar dialogo"
                        className="rounded-md px-2 py-1 text-sm font-medium text-slate-500 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-500"
                        onClick={onClose}
                        type="button"
                    >
                        x
                    </button>
                </div>

                <div className="mt-6">{children}</div>
            </div>
        </div>
    );
}
