import { Link } from '@inertiajs/react';
import { PropsWithChildren } from 'react';

type AppLayoutProps = PropsWithChildren<{
    titulo: string;
    subtitulo?: string;
}>;

export function AppLayout({ children, titulo, subtitulo }: AppLayoutProps) {
    return (
        <div className="min-h-screen bg-slate-50">
            <header className="border-b border-slate-200 bg-white">
                <div className="mx-auto flex max-w-7xl flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p className="text-sm font-semibold uppercase tracking-wide text-cyan-700">Planno</p>
                        <h1 className="mt-1 text-2xl font-semibold text-slate-950">{titulo}</h1>
                        {subtitulo ? <p className="mt-1 max-w-3xl text-sm text-slate-600">{subtitulo}</p> : null}
                    </div>
                    <nav className="flex flex-wrap gap-2 text-sm font-medium text-slate-700">
                        <Link className="rounded-md px-3 py-2 hover:bg-slate-100" href="/">
                            Inicio
                        </Link>
                        <Link className="rounded-md px-3 py-2 hover:bg-slate-100" href="/turmas">
                            Turmas
                        </Link>
                        <Link className="rounded-md px-3 py-2 hover:bg-slate-100" href="/cadastros-alunos/solicitar">
                            Cadastro aluno
                        </Link>
                        <Link className="rounded-md px-3 py-2 hover:bg-slate-100" href="/projetos">
                            Projetos
                        </Link>
                    </nav>
                </div>
            </header>
            <main className="mx-auto max-w-7xl px-6 py-8">{children}</main>
        </div>
    );
}
