'use client';

import * as React from 'react';
import { Moon, Sun } from 'lucide-react';
import { useTheme } from 'next-themes';

import { Button } from '@/components/ui/button';

const emptySubscribe = () => () => {};

function useIsMounted() {
    return React.useSyncExternalStore(
        emptySubscribe,
        () => true,
        () => false
    );
}

export function ThemeToggle() {
    const { setTheme, resolvedTheme } = useTheme();

    const isMounted = useIsMounted();

    if (!isMounted) {
        return <Button variant="outline" size="icon" className="w-8 h-8" disabled />;
    }

    const toggleTheme = () => {
        setTheme(resolvedTheme === 'dark' ? 'light' : 'dark');
    };

    return (
        <Button variant="outline" size="icon" onClick={toggleTheme} className="relative">
            <Sun className="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
            <Moon className="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
            <span className="sr-only">Toggle theme</span>
        </Button>
    );
}
