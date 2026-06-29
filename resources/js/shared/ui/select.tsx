import { Check, ChevronDown } from 'lucide-react';
import {
    useEffect,
    useId,
    useLayoutEffect,
    useRef,
    useState,
    type CSSProperties,
    type KeyboardEvent,
} from 'react';
import { createPortal } from 'react-dom';

import { cn } from '../lib/utils';

export type SelectOption = {
    label: string;
    value: string;
};

type SelectProps = {
    ariaLabel?: string;
    className?: string;
    disabled?: boolean;
    id?: string;
    invalid?: boolean;
    name?: string;
    onValueChange: (value: string) => void;
    options: SelectOption[];
    placeholder?: string;
    value: string;
};

type MenuPosition = {
    left: number;
    maxHeight: number;
    placement: 'bottom' | 'top';
    top: number;
    width: number;
};

export function Select({
    ariaLabel,
    className,
    disabled = false,
    id,
    invalid = false,
    name,
    onValueChange,
    options,
    placeholder = 'Selecione uma opção',
    value,
}: SelectProps) {
    const generatedId = useId();
    const triggerId = id ?? `select-${generatedId}`;
    const listboxId = `${triggerId}-listbox`;
    const rootRef = useRef<HTMLDivElement>(null);
    const triggerRef = useRef<HTMLButtonElement>(null);
    const menuRef = useRef<HTMLDivElement>(null);
    const searchRef = useRef({ query: '', timestamp: 0 });
    const [aberto, setAberto] = useState(false);
    const [indiceAtivo, setIndiceAtivo] = useState(0);
    const [position, setPosition] = useState<MenuPosition | null>(null);
    const selectedIndex = options.findIndex((option) => option.value === value);
    const selectedOption = selectedIndex >= 0 ? options[selectedIndex] : null;

    function updatePosition() {
        const trigger = triggerRef.current;

        if (!trigger) {
            return;
        }

        const rect = trigger.getBoundingClientRect();
        const menuEstimatedHeight = Math.min(options.length * 42 + 12, 280);
        const spaceBelow = window.innerHeight - rect.bottom - 12;
        const spaceAbove = rect.top - 12;
        const placement = spaceBelow < Math.min(menuEstimatedHeight, 180) && spaceAbove > spaceBelow ? 'top' : 'bottom';
        const availableSpace = placement === 'bottom' ? spaceBelow : spaceAbove;

        setPosition({
            left: Math.max(8, Math.min(rect.left, window.innerWidth - rect.width - 8)),
            maxHeight: Math.max(112, Math.min(280, availableSpace - 8)),
            placement,
            top: placement === 'bottom' ? rect.bottom + 6 : rect.top - 6,
            width: rect.width,
        });
    }

    function abrir() {
        if (disabled) {
            return;
        }

        setIndiceAtivo(selectedIndex >= 0 ? selectedIndex : 0);
        setAberto(true);
    }

    function fechar() {
        setAberto(false);
        setPosition(null);
    }

    function selecionar(index: number) {
        const option = options[index];

        if (!option) {
            return;
        }

        onValueChange(option.value);
        fechar();
        requestAnimationFrame(() => triggerRef.current?.focus());
    }

    function handleKeyDown(event: KeyboardEvent<HTMLButtonElement>) {
        if (disabled) {
            return;
        }

        if (!aberto && ['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(event.key)) {
            event.preventDefault();
            abrir();

            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            fechar();

            return;
        }

        if (event.key === 'Tab') {
            fechar();

            return;
        }

        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            const direction = event.key === 'ArrowDown' ? 1 : -1;
            setIndiceAtivo((current) => (current + direction + options.length) % options.length);

            return;
        }

        if (event.key === 'Home' || event.key === 'End') {
            event.preventDefault();
            setIndiceAtivo(event.key === 'Home' ? 0 : Math.max(0, options.length - 1));

            return;
        }

        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            selecionar(indiceAtivo);

            return;
        }

        if (event.key.length === 1 && /\S/.test(event.key)) {
            const now = Date.now();
            const currentSearch = searchRef.current;
            const query = now - currentSearch.timestamp > 600 ? event.key : `${currentSearch.query}${event.key}`;
            searchRef.current = { query: query.toLocaleLowerCase('pt-BR'), timestamp: now };
            const matchIndex = options.findIndex((option) =>
                option.label.toLocaleLowerCase('pt-BR').startsWith(searchRef.current.query),
            );

            if (matchIndex >= 0) {
                setIndiceAtivo(matchIndex);
            }
        }
    }

    useLayoutEffect(() => {
        if (aberto) {
            updatePosition();
        }
    }, [aberto]);

    useEffect(() => {
        if (!aberto) {
            return;
        }

        function handlePointerDown(event: PointerEvent) {
            const target = event.target as Node;

            if (!rootRef.current?.contains(target) && !menuRef.current?.contains(target)) {
                fechar();
            }
        }

        document.addEventListener('pointerdown', handlePointerDown);
        window.addEventListener('resize', updatePosition);
        window.addEventListener('scroll', updatePosition, true);

        return () => {
            document.removeEventListener('pointerdown', handlePointerDown);
            window.removeEventListener('resize', updatePosition);
            window.removeEventListener('scroll', updatePosition, true);
        };
    }, [aberto, options.length]);

    useEffect(() => {
        if (aberto) {
            menuRef.current?.querySelector<HTMLElement>(`[data-option-index="${indiceAtivo}"]`)?.scrollIntoView({
                block: 'nearest',
            });
        }
    }, [aberto, indiceAtivo]);

    const menuStyle: CSSProperties | undefined = position
        ? {
              left: position.left,
              maxHeight: position.maxHeight,
              top: position.top,
              width: position.width,
          }
        : undefined;

    return (
        <div className={cn('relative', className)} ref={rootRef}>
            {name ? <input name={name} type="hidden" value={value} /> : null}
            <button
                aria-activedescendant={aberto ? `${listboxId}-option-${indiceAtivo}` : undefined}
                aria-autocomplete="none"
                aria-controls={aberto ? listboxId : undefined}
                aria-expanded={aberto}
                aria-haspopup="listbox"
                aria-invalid={invalid || undefined}
                aria-label={ariaLabel}
                className="dropdown-trigger flex h-10 w-full items-center justify-between gap-3 rounded-md border border-[#b9c4b7] bg-white px-3 text-left text-sm text-[#17211f] outline-none disabled:cursor-not-allowed disabled:bg-[#f1f3ed] disabled:text-[#8a9691]"
                disabled={disabled}
                id={triggerId}
                onClick={() => (aberto ? fechar() : abrir())}
                onKeyDown={handleKeyDown}
                ref={triggerRef}
                role="combobox"
                type="button"
            >
                <span className={cn('min-w-0 truncate', (!selectedOption || selectedOption.value === '') && 'text-[#87948f]')}>
                    {selectedOption?.label ?? placeholder}
                </span>
                <ChevronDown
                    aria-hidden="true"
                    className={cn('h-4 w-4 shrink-0 text-[#66756f] transition-transform duration-200', aberto && 'rotate-180')}
                />
            </button>

            {aberto && position
                ? createPortal(
                      <div
                          aria-label={ariaLabel}
                          aria-labelledby={ariaLabel ? undefined : triggerId}
                          className="dropdown-menu fixed z-[100] overflow-y-auto rounded-lg border border-[#ccd7ca] bg-white p-1.5 shadow-[0_18px_45px_-18px_rgba(23,33,31,0.38)]"
                          data-placement={position.placement}
                          id={listboxId}
                          ref={menuRef}
                          role="listbox"
                          style={menuStyle}
                      >
                          {options.map((option, index) => {
                              const selected = option.value === value;
                              const active = index === indiceAtivo;

                              return (
                                  <div
                                      aria-selected={selected}
                                      className={cn(
                                          'dropdown-option flex min-h-10 cursor-pointer items-center justify-between gap-3 rounded-md px-3 py-2 text-sm text-[#30403c] outline-none',
                                          active && 'bg-[#eff5ed] text-[#17211f]',
                                          selected && 'font-semibold text-[#0f766e]',
                                      )}
                                      data-option-index={index}
                                      id={`${listboxId}-option-${index}`}
                                      key={option.value}
                                      onClick={() => selecionar(index)}
                                      onMouseEnter={() => setIndiceAtivo(index)}
                                      role="option"
                                  >
                                      <span>{option.label}</span>
                                      <Check className={cn('h-4 w-4 shrink-0', selected ? 'opacity-100' : 'opacity-0')} />
                                  </div>
                              );
                          })}
                      </div>,
                      document.body,
                  )
                : null}
        </div>
    );
}
