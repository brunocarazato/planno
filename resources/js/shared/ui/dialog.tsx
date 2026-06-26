import { PropsWithChildren, useEffect, useRef } from 'react';
import { X } from 'lucide-react';

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
            className="fixed inset-0 z-50 flex items-center justify-center bg-[#17211f]/45 px-4 py-6"
            onClick={onClose}
            role="dialog"
        >
            <div
                className={cn(
                    'w-full max-w-lg rounded-lg border border-[#dfe5d8] bg-white p-6 shadow-2xl shadow-[#17211f]/12 outline-none',
                    className,
                )}
                onClick={(event) => event.stopPropagation()}
                ref={dialogRef}
                tabIndex={-1}
            >
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <h2 className="text-lg font-semibold text-[#17211f]">{titulo}</h2>
                        {descricao ? <p className="mt-2 text-sm leading-6 text-[#53635e]">{descricao}</p> : null}
                    </div>
                    <button
                        aria-label="Fechar dialogo"
                        className="inline-flex h-9 w-9 items-center justify-center rounded-md text-[#66756f] hover:bg-[#f4f7ef] hover:text-[#17211f] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#66756f]"
                        onClick={onClose}
                        type="button"
                    >
                        <X className="h-4 w-4" />
                    </button>
                </div>

                <div className="mt-6">{children}</div>
            </div>
        </div>
    );
}
