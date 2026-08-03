import { Button } from '@/components/ui/button';
import { ThemeToggle } from '@/components/theme-toggle';

/**
 * Render the default Home page layout for the application root.
 *
 * Displays a centered container with Next.js and Vercel branding, a heading prompting edits to page.tsx,
 * links to Templates and Learning resources, and action buttons for "Deploy Now" and "Documentation".
 *
 * @returns The React element for the Home page.
 */
export default function Home() {
    return (
        <main className="flex min-h-screen flex-col items-center justify-center p-24 bg-background text-foreground">
            <div className="absolute top-6 right-6">
                <ThemeToggle />
            </div>

            <div className="flex flex-col items-center gap-6">
                <span className="font-mono text-xs px-3 py-1 rounded-full bg-muted text-muted-foreground border border-border">
                    Status: Ready for Phase 3 (i18n)
                </span>
                <h1 className="text-4xl font-bold tracking-tight sm:text-5xl">
                    SogoHlopec Portfolio V2
                </h1>

                <p className="text-muted-foreground max-w-md">
                    Привет! Это обновленное портфолио на Next.js, TypeScript и Tailwind CSS.
                </p>
                <div className="flex gap-4">
                    <Button variant="default">Связаться</Button>
                    <Button variant="outline">Мои проекты</Button>
                </div>
            </div>
        </main>
    );
}
