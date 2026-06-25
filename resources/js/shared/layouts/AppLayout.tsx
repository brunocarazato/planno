import { Link, router, usePage } from '@inertiajs/react';
import { PropsWithChildren } from 'react';

type AppLayoutProps = PropsWithChildren<{
    titulo: string;
    subtitulo?: string;
}>;

export function AppLayout({ children, titulo, subtitulo }: AppLayoutProps) {
    const { auth } = usePage<{
        auth?: {
            user?: {
                name: string;
                ra: string | null;
                tipo: string;
            } | null;
        };
    }>().props;
    const usuario = auth?.user;
    const ehProfessor = usuario?.tipo === 'professor';

    function sair() {
        router.post('/sair');
    }

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
                        {ehProfessor ? (
                            <Link className="rounded-md px-3 py-2 hover:bg-slate-100" href="/turmas">
                                Turmas
                            </Link>
                        ) : null}
                        <Link className="rounded-md px-3 py-2 hover:bg-slate-100" href="/cadastros-alunos/solicitar">
                            Cadastro aluno
                        </Link>
                        <Link className="rounded-md px-3 py-2 hover:bg-slate-100" href="/projetos">
                            Projetos
                        </Link>
                        {usuario ? (
                            <button
                                className="rounded-md px-3 py-2 text-left hover:bg-slate-100"
                                onClick={sair}
                                type="button"
                            >
                                Sair de {usuario.name}
                            </button>
                        ) : (
                            <Link className="rounded-md px-3 py-2 hover:bg-slate-100" href="/entrar">
                                Entrar
                            </Link>
                        )}
                    </nav>
                </div>
            </header>
            <main className="mx-auto max-w-7xl px-6 py-8">{children}</main>
        </div>
    );
}
