import type { Metadata } from 'next';
import { fontSans, fontMono } from '@/lib/fonts';
import { cn } from '@/lib/utils';
import '../globals.css';
import { ThemeProvider } from '@/components/theme-provider';
import { hasLocale } from '@/lib/i18n';
import { notFound } from 'next/navigation';

export const metadata: Metadata = {
    title: 'Developer Portfolio',
    description: 'Personal portfolio showcasing projects and skills',
};

export function generateStaticParams() {
    return [{ lang: 'en' }, { lang: 'ru' }];
}

export const dynamicParams = false;

export default async function RootLayout({
    children,
    params,
}: Readonly<{
    children: React.ReactNode;
    params: Promise<{ lang: string }>;
}>) {
    const { lang } = await params;
    if (!hasLocale(lang)) {
        notFound();
    }

    return (
        <html
            lang={lang}
            suppressHydrationWarning
            className={cn(
                'min-h-screen bg-background font-sans antialiased',
                fontSans.variable,
                fontMono.variable
            )}
        >
            <body className="min-h-full flex flex-col">
                <ThemeProvider
                    attribute="class"
                    defaultTheme="system"
                    enableSystem
                    disableTransitionOnChange
                >
                    {children}
                </ThemeProvider>
            </body>
        </html>
    );
}
