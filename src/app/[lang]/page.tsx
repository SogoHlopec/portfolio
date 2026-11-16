import { notFound } from 'next/navigation';

import { Button } from '@/components/ui/button';
import { ThemeToggle } from '@/components/theme-toggle';
import { getDictionary, hasLocale } from '@/lib/i18n';

export default async function Home({
    params,
}: Readonly<{
    params: Promise<{ lang: string }>;
}>) {
    const { lang } = await params;
    if (!hasLocale(lang)) {
        notFound();
    }

    const dict = await getDictionary(lang);

    return (
        <main className="flex min-h-screen flex-col items-center justify-center p-24 bg-background text-foreground">
            <div className="absolute top-6 right-6">
                <ThemeToggle />
            </div>

            <div className="flex flex-col items-center gap-6">
                <span className="font-mono text-xs px-3 py-1 rounded-full bg-muted text-muted-foreground border border-border">
                    {dict.home.status}
                </span>
                <h1 className="text-4xl font-bold tracking-tight sm:text-5xl">{dict.home.title}</h1>

                <p className="text-muted-foreground max-w-md">{dict.home.subtitle}</p>
                <div className="flex gap-4">
                    <Button variant="default">{dict.home.ctaContact}</Button>
                    <Button variant="outline">{dict.home.ctaProjects}</Button>
                </div>
            </div>
        </main>
    );
}
