import type { Metadata } from 'next';
import { fontSans, fontMono } from '@/lib/fonts';
import { cn } from '@/lib/utils';
import './globals.css';
import { ThemeProvider } from '@/components/theme-provider';

export const metadata: Metadata = {
    title: 'Developer Portfolio',
    description: 'Personal portfolio showcasing projects and skills',
};

export default function RootLayout({
    children,
}: Readonly<{
    children: React.ReactNode;
}>) {
    return (
        <html
            lang="en"
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
