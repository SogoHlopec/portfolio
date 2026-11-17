'use client';

import * as React from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { Check, Globe } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

const LANGUAGES = [
    { code: 'en', label: 'English' },
    { code: 'ru', label: 'Русский' },
] as const;

export function LanguageSwitcher({ lang }: { lang: string }) {
    const pathname = usePathname();
    const [open, setOpen] = React.useState(false);
    const rootRef = React.useRef<HTMLDivElement>(null);

    React.useEffect(() => {
        if (!open) {
            return;
        }
        const onMouseDown = (event: MouseEvent) => {
            if (!rootRef.current?.contains(event.target as Node)) {
                setOpen(false);
            }
        };
        const onKeyDown = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                setOpen(false);
            }
        };
        document.addEventListener('mousedown', onMouseDown);
        document.addEventListener('keydown', onKeyDown);
        return () => {
            document.removeEventListener('mousedown', onMouseDown);
            document.removeEventListener('keydown', onKeyDown);
        };
    }, [open]);

    const hrefFor = (code: string) => {
        const segments = pathname.split('/');
        segments[1] = code;
        return segments.join('/') || `/${code}`;
    };

    return (
        <div ref={rootRef} className="relative">
            <Button
                variant="outline"
                size="icon"
                className="w-8 h-8"
                aria-label="Switch language"
                aria-haspopup="menu"
                aria-expanded={open}
                onClick={() => setOpen((value) => !value)}
            >
                <Globe className="h-[1.2rem] w-[1.2rem]" />
            </Button>
            {open && (
                <div
                    role="menu"
                    aria-label="Languages"
                    className="absolute right-0 top-full z-50 mt-2 min-w-32 rounded-md border border-border bg-popover text-popover-foreground shadow-md"
                >
                    {LANGUAGES.map(({ code, label }) => (
                        <Link
                            key={code}
                            href={hrefFor(code)}
                            role="menuitemradio"
                            aria-checked={code === lang}
                            onClick={() => setOpen(false)}
                            className={cn(
                                'flex w-full items-center justify-between gap-2 px-3 py-1.5 text-sm transition-colors hover:bg-muted',
                                code === lang && 'font-medium'
                            )}
                        >
                            {label}
                            {code === lang && <Check className="h-4 w-4" aria-hidden="true" />}
                        </Link>
                    ))}
                </div>
            )}
        </div>
    );
}
