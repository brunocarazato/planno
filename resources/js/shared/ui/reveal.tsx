import { useEffect, useRef, useState, type CSSProperties, type PropsWithChildren } from 'react';

import { cn } from '../lib/utils';

type RevealProps = PropsWithChildren<{
    className?: string;
    delay?: number;
    distance?: 'short' | 'medium';
}>;

type RevealStyle = CSSProperties & {
    '--reveal-delay': `${number}ms`;
    '--reveal-distance': string;
};

export function Reveal({ children, className, delay = 0, distance = 'medium' }: RevealProps) {
    const elementRef = useRef<HTMLDivElement>(null);
    const [visivel, setVisivel] = useState(false);

    useEffect(() => {
        const element = elementRef.current;

        if (!element || !('IntersectionObserver' in window)) {
            setVisivel(true);

            return;
        }

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setVisivel(true);
                    observer.disconnect();
                }
            },
            { rootMargin: '0px 0px -8% 0px', threshold: 0.12 },
        );

        observer.observe(element);

        return () => observer.disconnect();
    }, []);

    const style: RevealStyle = {
        '--reveal-delay': `${delay}ms`,
        '--reveal-distance': distance === 'short' ? '12px' : '24px',
    };

    return (
        <div className={cn('motion-reveal', visivel && 'is-visible', className)} ref={elementRef} style={style}>
            {children}
        </div>
    );
}
