import { Button } from '@/components/ui/button';

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
            <div className="flex flex-col items-center gap-6">
                <h1 className="text-4xl font-bold tracking-tight">SogoHlopec Portfolio V2</h1>
                <p className="text-muted-foreground text-center max-w-md">
                    Проект успешно инициализирован. Базовые стили и дизайн-система shadcn/ui
                    настроены.
                </p>
                <div className="flex gap-4">
                    <Button variant="default">Связаться</Button>
                    <Button variant="outline">Мои проекты</Button>
                </div>
            </div>
        </main>
    );
}
