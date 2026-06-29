import { Link, router, usePage } from '@inertiajs/react';
import { FolderKanban, House, LayoutDashboard, LogIn, LogOut, Route, School, UserPlus, UsersRound } from 'lucide-react';
import { PropsWithChildren, useState } from 'react';

import { LoginDialog } from '../autenticacao/LoginDialog';

type AppLayoutProps = PropsWithChildren<{
    titulo: string;
    subtitulo?: string;
}>;

export function AppLayout({ children, titulo, subtitulo }: AppLayoutProps) {
    const { props, url } = usePage<{
        auth?: {
            user?: {
                name: string;
                ra: string | null;
                tipo: string;
            } | null;
        };
        navegacao?: {
            alunosAguardandoAprovacao?: number;
        };
    }>();
    const { auth } = props;
    const usuario = auth?.user;
    const ehProfessor = usuario?.tipo === 'professor';
    const alunosAguardandoAprovacao = props.navegacao?.alunosAguardandoAprovacao ?? 0;
    const rotaAtual = url.split('?')[0];
    const exibirMenuProjetos = Boolean(usuario) || rotaAtual !== '/cadastros-alunos/solicitar';
    const [loginAberto, setLoginAberto] = useState(false);

    function sair() {
        router.post('/sair');
    }

    return (
        <div className="min-h-screen bg-[#f6f7f2] text-[#17211f]">
            <header className="app-header relative isolate overflow-hidden border-b border-[#dfe5d8] bg-[#fbfcf7]">
                <div className="absolute inset-0 -z-10 bg-[linear-gradient(rgba(23,33,31,0.055)_1px,transparent_1px),linear-gradient(90deg,rgba(23,33,31,0.055)_1px,transparent_1px)] bg-[size:44px_44px]" />
                <div className="absolute left-0 top-0 -z-10 h-full w-1/3 bg-[#eff5ed]" />

                <div className="mx-auto flex max-w-7xl flex-col gap-5 px-5 py-5 sm:px-8">
                    <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <Link className="inline-flex items-center gap-3 text-[#17211f]" href="/">
                            <span className="brand-mark flex h-10 w-10 items-center justify-center rounded-lg bg-[#17211f] text-white">
                                <Route className="h-5 w-5" />
                            </span>
                            <span className="text-xl font-semibold">Planno</span>
                        </Link>

                        <nav className="flex flex-wrap gap-2 text-sm font-semibold text-[#51605c]">
                            <NavLink ativo={url === '/'} href="/">
                                <House className="h-4 w-4" />
                                Início
                            </NavLink>
                            {ehProfessor ? (
                                <NavLink ativo={url.startsWith('/dashboard')} href="/dashboard">
                                    <LayoutDashboard className="h-4 w-4" />
                                    Dashboard
                                </NavLink>
                            ) : null}
                            {ehProfessor ? (
                                <NavLink ativo={url.startsWith('/turmas')} href="/turmas">
                                    <School className="h-4 w-4" />
                                    Turmas
                                </NavLink>
                            ) : null}
                            {ehProfessor ? (
                                <NavLink ativo={url.startsWith('/alunos')} href="/alunos">
                                    <UsersRound className="h-4 w-4" />
                                    Alunos
                                    {alunosAguardandoAprovacao > 0 ? (
                                        <span
                                            aria-label={`${alunosAguardandoAprovacao} aluno${alunosAguardandoAprovacao === 1 ? '' : 's'} aguardando aprovação`}
                                            className="notification-badge inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#f06f45] px-1.5 text-[0.68rem] font-bold leading-none text-white shadow-sm"
                                            title={`${alunosAguardandoAprovacao} aguardando aprovação`}
                                        >
                                            {alunosAguardandoAprovacao}
                                        </span>
                                    ) : null}
                                </NavLink>
                            ) : null}
                            {!usuario ? (
                                <NavLink ativo={url.startsWith('/cadastros-alunos')} href="/cadastros-alunos/solicitar">
                                    <UserPlus className="h-4 w-4" />
                                    Solicitar cadastro
                                </NavLink>
                            ) : null}
                            {exibirMenuProjetos ? (
                                <NavLink ativo={url.startsWith('/projetos')} href="/projetos">
                                    <FolderKanban className="h-4 w-4" />
                                    Projetos
                                </NavLink>
                            ) : null}
                            {usuario ? (
                                <button
                                    className="inline-flex h-10 cursor-pointer items-center gap-2 rounded-md px-3 text-left hover:bg-[#edf2e9] hover:text-[#17211f]"
                                    onClick={sair}
                                    type="button"
                                >
                                    <LogOut className="h-4 w-4" />
                                    Sair de {usuario.name}
                                </button>
                            ) : (
                                <button
                                    className="inline-flex h-10 cursor-pointer items-center gap-2 rounded-md px-3 transition hover:bg-[#edf2e9] hover:text-[#17211f]"
                                    onClick={() => setLoginAberto(true)}
                                    type="button"
                                >
                                    <LogIn className="h-4 w-4" />
                                    Entrar
                                </button>
                            )}
                        </nav>
                    </div>

                    <div className="max-w-4xl pb-4 pt-2">
                        <p className="text-sm font-bold text-[#0f766e]">Ambiente de aprendizagem</p>
                        <h1 className="mt-2 text-3xl font-semibold leading-tight text-[#17211f] md:text-4xl">{titulo}</h1>
                        {subtitulo ? <p className="mt-3 max-w-3xl text-base leading-7 text-[#53635e]">{subtitulo}</p> : null}
                    </div>
                </div>
            </header>
            <main className="page-content mx-auto max-w-7xl px-5 py-8 sm:px-8">{children}</main>
            <LoginDialog aberto={loginAberto} onClose={() => setLoginAberto(false)} />
        </div>
    );
}

function NavLink({ ativo, children, href }: PropsWithChildren<{ ativo: boolean; href: string }>) {
    return (
        <Link
            className={`nav-link inline-flex h-10 items-center gap-2 rounded-md px-3 ${
                ativo ? 'bg-[#17211f] text-white shadow-sm' : 'hover:bg-[#edf2e9] hover:text-[#17211f]'
            }`}
            href={href}
        >
            {children}
        </Link>
    );
}
