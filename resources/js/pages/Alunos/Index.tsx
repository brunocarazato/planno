import { Head, router, useForm } from '@inertiajs/react';
import {
    CalendarDays,
    Check,
    Clock3,
    GraduationCap,
    Search,
    UserCheck,
    UserPlus,
    UsersRound,
    X,
} from 'lucide-react';
import { FormEvent, ReactNode, useMemo, useState } from 'react';

import { AppLayout } from '../../shared/layouts/AppLayout';
import { Button } from '../../shared/ui/button';
import { Dialog } from '../../shared/ui/dialog';
import { Select } from '../../shared/ui/select';

type StatusCadastro = 'aprovado' | 'expirado' | 'pendente' | 'reprovado';

type CadastroAluno = {
    id: number;
    nome: string;
    ra: string;
    status: StatusCadastro;
    motivoReprovacao: string | null;
    validoAte: string | null;
    criadoEm: string | null;
    turma: {
        id: number;
        nome: string;
        codigo: string;
    };
};

type Turma = {
    id: number;
    nome: string;
    codigo: string;
    periodoFormatado: string | null;
};

type CadastroForm = {
    turma_id: string;
    nome: string;
    ra: string;
    password: string;
    password_confirmation: string;
};

type AlunosIndexProps = {
    cadastros: CadastroAluno[];
    turmas: Turma[];
    turmasAtivas: Turma[];
    metricas: {
        total: number;
        pendentes: number;
        aprovadosAtivos: number;
    };
    flash?: {
        success?: string | null;
    };
};

const formularioInicial: CadastroForm = {
    turma_id: '',
    nome: '',
    ra: '',
    password: '',
    password_confirmation: '',
};

export default function AlunosIndex({ cadastros, turmas, turmasAtivas, metricas, flash }: AlunosIndexProps) {
    const [modalCadastroAberta, setModalCadastroAberta] = useState(false);
    const [cadastroParaReprovar, setCadastroParaReprovar] = useState<CadastroAluno | null>(null);
    const [motivoReprovacao, setMotivoReprovacao] = useState('');
    const [busca, setBusca] = useState('');
    const [status, setStatus] = useState<'todos' | StatusCadastro>('todos');
    const [turmaId, setTurmaId] = useState('todas');
    const form = useForm<CadastroForm>(formularioInicial);

    const cadastrosFiltrados = useMemo(() => {
        const termo = busca.trim().toLocaleLowerCase('pt-BR');

        return cadastros.filter((cadastro) => {
            const correspondeBusca =
                termo.length === 0 ||
                cadastro.nome.toLocaleLowerCase('pt-BR').includes(termo) ||
                cadastro.ra.toLocaleLowerCase('pt-BR').includes(termo) ||
                cadastro.turma.nome.toLocaleLowerCase('pt-BR').includes(termo) ||
                cadastro.turma.codigo.toLocaleLowerCase('pt-BR').includes(termo);
            const correspondeStatus = status === 'todos' || cadastro.status === status;
            const correspondeTurma = turmaId === 'todas' || String(cadastro.turma.id) === turmaId;

            return correspondeBusca && correspondeStatus && correspondeTurma;
        });
    }, [busca, cadastros, status, turmaId]);

    function fecharModalCadastro() {
        setModalCadastroAberta(false);
        form.reset();
        form.clearErrors();
    }

    function cadastrarAluno(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        form.post('/alunos', {
            preserveScroll: true,
            onSuccess: fecharModalCadastro,
        });
    }

    function aprovarCadastro(cadastro: CadastroAluno) {
        router.patch(`/cadastros-alunos/${cadastro.id}/aprovar`, {}, { preserveScroll: true });
    }

    function confirmarReprovacao() {
        if (!cadastroParaReprovar) {
            return;
        }

        router.patch(
            `/cadastros-alunos/${cadastroParaReprovar.id}/reprovar`,
            { motivo_reprovacao: motivoReprovacao },
            { preserveScroll: true },
        );
        setCadastroParaReprovar(null);
        setMotivoReprovacao('');
    }

    return (
        <AppLayout
            titulo="Alunos"
            subtitulo="Acompanhe solicitações, vínculos e acessos dos alunos em um só lugar."
        >
            <Head title="Alunos" />

            {flash?.success ? (
                <div className="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {flash.success}
                </div>
            ) : null}

            <section className="grid gap-4 md:grid-cols-3">
                <CardMetrica
                    apoio="Em todos os estados e turmas."
                    icone={<UsersRound className="h-5 w-5" />}
                    rotulo="Registros de alunos"
                    tom="bg-[#eff5ed] text-[#54732f]"
                    valor={metricas.total}
                />
                <CardMetrica
                    apoio="Aguardando sua avaliação."
                    icone={<Clock3 className="h-5 w-5" />}
                    rotulo="Solicitações pendentes"
                    tom="bg-[#fff7d6] text-[#8a6500]"
                    valor={metricas.pendentes}
                />
                <CardMetrica
                    apoio="Com vínculo válido no momento."
                    icone={<UserCheck className="h-5 w-5" />}
                    rotulo="Alunos aprovados"
                    tom="bg-[#e7f5f2] text-[#0f766e]"
                    valor={metricas.aprovadosAtivos}
                />
            </section>

            <section className="mt-6 overflow-hidden rounded-lg border border-[#dfe5d8] bg-white shadow-sm">
                <div className="flex flex-col gap-4 border-b border-[#dfe5d8] p-5 lg:flex-row lg:items-center lg:justify-between lg:p-6">
                    <div>
                        <div className="flex items-center gap-3">
                            <div className="rounded-md bg-[#eff5ed] p-2 text-[#0f766e]">
                                <GraduationCap className="h-5 w-5" />
                            </div>
                            <div>
                                <h2 className="text-lg font-semibold text-[#17211f]">Alunos cadastrados</h2>
                                <p className="text-sm text-[#53635e]">Consulte os vínculos e trate solicitações pendentes.</p>
                            </div>
                        </div>
                    </div>
                    <Button
                        disabled={turmasAtivas.length === 0}
                        onClick={() => setModalCadastroAberta(true)}
                        type="button"
                    >
                        <UserPlus className="h-4 w-4" />
                        Cadastrar aluno
                    </Button>
                </div>

                <div className="grid gap-3 border-b border-[#dfe5d8] bg-[#fbfcf7] p-4 md:grid-cols-[minmax(220px,1fr)_190px_220px]">
                    <label className="relative block">
                        <span className="sr-only">Buscar aluno</span>
                        <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#7b8984]" />
                        <input
                            className="h-10 w-full rounded-md border border-[#b9c4b7] bg-white pl-9 pr-3 text-sm text-[#17211f] outline-none transition placeholder:text-[#87948f] focus:border-[#0f766e] focus:ring-2 focus:ring-[#d9e2d7]"
                            onChange={(event) => setBusca(event.target.value)}
                            placeholder="Buscar por nome, RA ou turma"
                            type="search"
                            value={busca}
                        />
                    </label>
                    <div>
                        <Select
                            ariaLabel="Filtrar por status"
                            onValueChange={(value) => setStatus(value as 'todos' | StatusCadastro)}
                            options={[
                                { label: 'Todos os status', value: 'todos' },
                                { label: 'Pendente', value: 'pendente' },
                                { label: 'Aprovado', value: 'aprovado' },
                                { label: 'Reprovado', value: 'reprovado' },
                                { label: 'Expirado', value: 'expirado' },
                            ]}
                            value={status}
                        />
                    </div>
                    <div>
                        <Select
                            ariaLabel="Filtrar por turma"
                            onValueChange={setTurmaId}
                            options={[
                                { label: 'Todas as turmas', value: 'todas' },
                                ...turmas.map((turma) => ({
                                    label: `${turma.nome} (${turma.codigo})`,
                                    value: String(turma.id),
                                })),
                            ]}
                            value={turmaId}
                        />
                    </div>
                </div>

                {cadastrosFiltrados.length === 0 ? (
                    <div className="p-10 text-center">
                        <div className="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-[#f4f7ef] text-[#66756f]">
                            <UsersRound className="h-5 w-5" />
                        </div>
                        <p className="mt-4 font-medium text-[#17211f]">
                            {cadastros.length === 0 ? 'Nenhum aluno cadastrado ainda.' : 'Nenhum aluno encontrado.'}
                        </p>
                        <p className="mt-1 text-sm text-[#53635e]">
                            {cadastros.length === 0
                                ? 'Cadastre um aluno ou aguarde uma nova solicitação.'
                                : 'Tente ajustar os termos ou filtros da busca.'}
                        </p>
                    </div>
                ) : (
                    <div className="divide-y divide-[#dfe5d8]">
                        {cadastrosFiltrados.map((cadastro) => (
                            <article
                                className="grid gap-4 p-5 lg:grid-cols-[minmax(220px,1.2fr)_minmax(180px,0.9fr)_150px_auto] lg:items-center"
                                key={cadastro.id}
                            >
                                <div className="min-w-0">
                                    <div className="flex flex-wrap items-center gap-2">
                                        <h3 className="truncate font-semibold text-[#17211f]">{cadastro.nome}</h3>
                                        <StatusCadastroBadge status={cadastro.status} />
                                    </div>
                                    <p className="mt-1 text-sm text-[#53635e]">RA {cadastro.ra}</p>
                                    {cadastro.motivoReprovacao ? (
                                        <p className="mt-2 text-sm text-[#9f3d2d]">Motivo: {cadastro.motivoReprovacao}</p>
                                    ) : null}
                                </div>

                                <div>
                                    <p className="text-sm font-medium text-[#17211f]">{cadastro.turma.nome}</p>
                                    <p className="mt-1 text-xs text-[#66756f]">{cadastro.turma.codigo}</p>
                                </div>

                                <div className="text-sm text-[#53635e]">
                                    <div className="flex items-center gap-2">
                                        <CalendarDays className="h-4 w-4 text-[#7b8984]" />
                                        <span>{rotuloValidade(cadastro)}</span>
                                    </div>
                                </div>

                                <div className="flex flex-wrap gap-2 lg:justify-end">
                                    {cadastro.status === 'pendente' ? (
                                        <>
                                            <Button onClick={() => aprovarCadastro(cadastro)} size="sm" type="button">
                                                <Check className="h-4 w-4" />
                                                Aprovar
                                            </Button>
                                            <Button
                                                onClick={() => {
                                                    setCadastroParaReprovar(cadastro);
                                                    setMotivoReprovacao('');
                                                }}
                                                size="sm"
                                                type="button"
                                                variant="secondary"
                                            >
                                                <X className="h-4 w-4" />
                                                Reprovar
                                            </Button>
                                        </>
                                    ) : (
                                        <span className="text-xs text-[#7b8984]">Sem ações pendentes</span>
                                    )}
                                </div>
                            </article>
                        ))}
                    </div>
                )}

                <div className="border-t border-[#dfe5d8] bg-[#fbfcf7] px-5 py-3 text-xs text-[#66756f]">
                    Exibindo {cadastrosFiltrados.length} de {cadastros.length} registro(s)
                </div>
            </section>

            <Dialog
                aberto={modalCadastroAberta}
                className="max-w-2xl"
                descricao="O acesso será criado e aprovado imediatamente, com validade de 1 ano."
                onClose={fecharModalCadastro}
                titulo="Cadastrar aluno"
            >
                <form onSubmit={cadastrarAluno}>
                    <div className="grid gap-5 sm:grid-cols-2">
                        <div className="sm:col-span-2">
                            <label className="text-sm font-medium text-[#51605c]" htmlFor="turma_id">
                                Turma
                            </label>
                            <Select
                                className="mt-1"
                                id="turma_id"
                                invalid={Boolean(form.errors.turma_id)}
                                onValueChange={(value) => form.setData('turma_id', value)}
                                options={[
                                    { label: 'Selecione uma turma', value: '' },
                                    ...turmasAtivas.map((turma) => ({
                                        label: `${turma.nome} (${turma.codigo})`,
                                        value: String(turma.id),
                                    })),
                                ]}
                                value={form.data.turma_id}
                            />
                            <ErroCampo erro={form.errors.turma_id} />
                        </div>

                        <CampoTexto
                            className="sm:col-span-2"
                            erro={form.errors.nome}
                            id="nome"
                            label="Nome completo"
                            onChange={(valor) => form.setData('nome', valor)}
                            placeholder="Nome do aluno"
                            type="text"
                            value={form.data.nome}
                        />
                        <CampoTexto
                            className="sm:col-span-2"
                            erro={form.errors.ra}
                            id="ra"
                            label="RA"
                            onChange={(valor) => form.setData('ra', valor)}
                            placeholder="Registro acadêmico"
                            type="text"
                            value={form.data.ra}
                        />
                        <CampoTexto
                            erro={form.errors.password}
                            id="password"
                            label="Senha inicial"
                            onChange={(valor) => form.setData('password', valor)}
                            placeholder="Mínimo de 8 caracteres"
                            type="password"
                            value={form.data.password}
                        />
                        <CampoTexto
                            erro={form.errors.password_confirmation}
                            id="password_confirmation"
                            label="Confirmar senha"
                            onChange={(valor) => form.setData('password_confirmation', valor)}
                            placeholder="Repita a senha"
                            type="password"
                            value={form.data.password_confirmation}
                        />
                    </div>

                    <div className="mt-6 flex flex-wrap justify-end gap-3 border-t border-[#e7ebe2] pt-5">
                        <Button disabled={form.processing} onClick={fecharModalCadastro} type="button" variant="secondary">
                            Cancelar
                        </Button>
                        <Button disabled={form.processing} type="submit">
                            <UserCheck className="h-4 w-4" />
                            {form.processing ? 'Cadastrando...' : 'Cadastrar e aprovar'}
                        </Button>
                    </div>
                </form>
            </Dialog>

            <Dialog
                aberto={Boolean(cadastroParaReprovar)}
                descricao={
                    cadastroParaReprovar
                        ? `Informe o motivo da reprovação de ${cadastroParaReprovar.nome}, se quiser registrar essa justificativa.`
                        : undefined
                }
                onClose={() => setCadastroParaReprovar(null)}
                titulo="Reprovar cadastro?"
            >
                <div>
                    <label className="text-sm font-medium text-[#51605c]" htmlFor="motivo_reprovacao">
                        Motivo da reprovação
                    </label>
                    <textarea
                        className="mt-1 min-h-28 w-full rounded-md border border-[#b9c4b7] px-3 py-2 text-sm text-[#17211f] outline-none transition focus:border-[#0f766e] focus:ring-2 focus:ring-[#d9e2d7]"
                        id="motivo_reprovacao"
                        onChange={(event) => setMotivoReprovacao(event.target.value)}
                        placeholder="Opcional"
                        value={motivoReprovacao}
                    />
                </div>
                <div className="mt-6 flex flex-wrap justify-end gap-3">
                    <Button onClick={() => setCadastroParaReprovar(null)} type="button" variant="secondary">
                        Cancelar
                    </Button>
                    <Button onClick={confirmarReprovacao} type="button">
                        Reprovar cadastro
                    </Button>
                </div>
            </Dialog>
        </AppLayout>
    );
}

function CardMetrica({
    apoio,
    icone,
    rotulo,
    tom,
    valor,
}: {
    apoio: string;
    icone: ReactNode;
    rotulo: string;
    tom: string;
    valor: number;
}) {
    return (
        <article className="rounded-lg border border-[#dfe5d8] bg-white p-5 shadow-sm">
            <div className="flex items-start justify-between gap-4">
                <div>
                    <p className="text-sm font-medium text-[#53635e]">{rotulo}</p>
                    <p className="mt-2 text-3xl font-semibold tracking-tight text-[#17211f]">{valor}</p>
                    <p className="mt-2 text-xs text-[#7b8984]">{apoio}</p>
                </div>
                <div className={`rounded-md p-2.5 ${tom}`}>{icone}</div>
            </div>
        </article>
    );
}

function StatusCadastroBadge({ status }: { status: StatusCadastro }) {
    const estilos: Record<StatusCadastro, string> = {
        aprovado: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        expirado: 'border-[#dfe5d8] bg-[#f4f7ef] text-[#66756f]',
        pendente: 'border-amber-200 bg-amber-50 text-amber-800',
        reprovado: 'border-red-200 bg-red-50 text-red-700',
    };
    const rotulos: Record<StatusCadastro, string> = {
        aprovado: 'Aprovado',
        expirado: 'Expirado',
        pendente: 'Pendente',
        reprovado: 'Reprovado',
    };

    return <span className={`rounded-full border px-2.5 py-1 text-xs font-medium ${estilos[status]}`}>{rotulos[status]}</span>;
}

function CampoTexto({
    className,
    erro,
    id,
    label,
    onChange,
    placeholder,
    type,
    value,
}: {
    className?: string;
    erro?: string;
    id: keyof Pick<CadastroForm, 'nome' | 'password' | 'password_confirmation' | 'ra'>;
    label: string;
    onChange: (valor: string) => void;
    placeholder: string;
    type: 'password' | 'text';
    value: string;
}) {
    return (
        <div className={className}>
            <label className="text-sm font-medium text-[#51605c]" htmlFor={id}>
                {label}
            </label>
            <input
                className="mt-1 w-full rounded-md border border-[#b9c4b7] px-3 py-2 text-sm text-[#17211f] outline-none transition focus:border-[#0f766e] focus:ring-2 focus:ring-[#d9e2d7]"
                id={id}
                onChange={(event) => onChange(event.target.value)}
                placeholder={placeholder}
                type={type}
                value={value}
            />
            <ErroCampo erro={erro} />
        </div>
    );
}

function ErroCampo({ erro }: { erro?: string }) {
    return erro ? <p className="mt-1 text-sm text-red-600">{erro}</p> : null;
}

function rotuloValidade(cadastro: CadastroAluno): string {
    if (cadastro.status === 'pendente') {
        return 'Aguardando avaliação';
    }

    if (!cadastro.validoAte) {
        return 'Validade não informada';
    }

    const data = new Date(`${cadastro.validoAte}T12:00:00`);
    const dataFormatada = new Intl.DateTimeFormat('pt-BR').format(data);

    return cadastro.status === 'expirado' ? `Expirou em ${dataFormatada}` : `Válido até ${dataFormatada}`;
}
