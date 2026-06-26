import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, ClipboardCheck, Save, School, Target, UserRound } from 'lucide-react';
import { FormEvent } from 'react';

import { AppLayout } from '../../shared/layouts/AppLayout';
import { Button, buttonVariants } from '../../shared/ui/button';

type Projeto = {
    id: number;
    nome: string;
    codigo: string;
    descricao: string | null;
    situacaoFormatada: string;
    turma: {
        id: number;
        nome: string;
        codigo: string;
        periodo: string | null;
        ano: number | null;
        periodoFormatado: string | null;
    };
    responsavel: UsuarioResumo;
    termoDeAbertura: {
        objetivo: string | null;
        justificativa: string | null;
        restricoes: string | null;
        premissas: string | null;
        entregasEsperadas: string | null;
    };
};

type UsuarioResumo = {
    id: number | null;
    name: string | null;
    ra: string | null;
    tipo: string | null;
};

type UsuarioOpcao = {
    id: number;
    name: string;
    ra: string | null;
    tipo: string;
};

type TermoForm = {
    objetivo: string;
    justificativa: string;
    restricoes: string;
    premissas: string;
    entregas_esperadas: string;
};

type ResponsavelForm = {
    responsavel_id: string;
};

type ProjetosShowProps = {
    projeto: Projeto;
    podeAlterarResponsavel: boolean;
    responsaveisDisponiveis: UsuarioOpcao[];
    flash?: {
        success?: string | null;
    };
};

export default function ProjetosShow({
    projeto,
    podeAlterarResponsavel,
    responsaveisDisponiveis,
    flash,
}: ProjetosShowProps) {
    const form = useForm<TermoForm>({
        objetivo: projeto.termoDeAbertura.objetivo ?? '',
        justificativa: projeto.termoDeAbertura.justificativa ?? '',
        restricoes: projeto.termoDeAbertura.restricoes ?? '',
        premissas: projeto.termoDeAbertura.premissas ?? '',
        entregas_esperadas: projeto.termoDeAbertura.entregasEsperadas ?? '',
    });
    const responsavelForm = useForm<ResponsavelForm>({
        responsavel_id: projeto.responsavel.id ? String(projeto.responsavel.id) : '',
    });

    function enviarFormulario(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        form.put(`/projetos/${projeto.id}/termo-de-abertura`, {
            preserveScroll: true,
        });
    }

    function enviarResponsavel(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        responsavelForm.patch(`/projetos/${projeto.id}/responsavel`, {
            preserveScroll: true,
        });
    }

    const nomeResponsavel = projeto.responsavel.name ?? 'Nao definido';

    return (
        <AppLayout titulo={projeto.nome} subtitulo="Detalhe do projeto didatico e termo de abertura inicial.">
            <Head title={projeto.nome} />

            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <Link className={buttonVariants({ variant: 'secondary' })} href="/projetos">
                    <ArrowLeft className="h-4 w-4" />
                    Voltar para projetos
                </Link>
                <span className="rounded-full border border-[#cdd9cf] bg-[#eff5ed] px-3 py-1 text-sm font-medium text-[#0d625c]">
                    {projeto.situacaoFormatada}
                </span>
            </div>

            {flash?.success ? (
                <div className="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {flash.success}
                </div>
            ) : null}

            <section className="grid gap-4 md:grid-cols-4">
                <Resumo icon={ClipboardCheck} rotulo="Codigo" valor={projeto.codigo} />
                <Resumo icon={School} rotulo="Turma" valor={`${projeto.turma.nome} (${projeto.turma.codigo})`} />
                <Resumo icon={UserRound} rotulo="Responsavel" valor={nomeResponsavel} />
                <Resumo icon={Target} rotulo="Situacao" valor={projeto.situacaoFormatada} />
            </section>

            <section className="mt-8 grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
                <div className="rounded-lg border border-[#dfe5d8] bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-semibold text-[#17211f]">Resumo do projeto</h2>
                    <p className="mt-3 text-sm leading-6 text-[#53635e]">
                        {projeto.descricao || 'Descricao ainda nao informada para este projeto.'}
                    </p>
                    <dl className="mt-6 space-y-4 text-sm">
                        <div>
                            <dt className="font-medium text-[#51605c]">Periodo da turma</dt>
                            <dd className="mt-1 text-[#53635e]">{projeto.turma.periodoFormatado || 'Nao informado'}</dd>
                        </div>
                        <div>
                            <dt className="font-medium text-[#51605c]">Responsavel</dt>
                            <dd className="mt-1 text-[#53635e]">
                                {nomeResponsavel}
                                {projeto.responsavel.ra ? ` (${projeto.responsavel.ra})` : null}
                            </dd>
                        </div>
                        <div>
                            <dt className="font-medium text-[#51605c]">Proxima etapa</dt>
                            <dd className="mt-1 text-[#53635e]">
                                Completar o termo de abertura para seguir para a trilha dos grupos de processos.
                            </dd>
                        </div>
                    </dl>

                    {podeAlterarResponsavel ? (
                        <form className="mt-6 border-t border-[#dfe5d8] pt-5" onSubmit={enviarResponsavel}>
                            <label className="text-sm font-medium text-[#51605c]" htmlFor="responsavel_id">
                                Alterar responsavel
                            </label>
                            <select
                                className="mt-1 w-full rounded-md border border-[#b9c4b7] bg-white px-3 py-2 text-sm text-[#17211f] outline-none transition focus:border-[#0f766e] focus:ring-2 focus:ring-[#d9e2d7]"
                                id="responsavel_id"
                                onChange={(event) => responsavelForm.setData('responsavel_id', event.target.value)}
                                value={responsavelForm.data.responsavel_id}
                            >
                                <option value="">Selecione um responsavel</option>
                                {responsaveisDisponiveis.map((responsavel) => (
                                    <option key={responsavel.id} value={String(responsavel.id)}>
                                        {responsavel.name}
                                        {responsavel.ra ? ` (${responsavel.ra})` : ''}
                                    </option>
                                ))}
                            </select>
                            {responsavelForm.errors.responsavel_id ? (
                                <p className="mt-1 text-sm text-red-600">{responsavelForm.errors.responsavel_id}</p>
                            ) : null}
                            <Button className="mt-3" disabled={responsavelForm.processing} size="sm" type="submit">
                                <Save className="h-4 w-4" />
                                Salvar responsavel
                            </Button>
                        </form>
                    ) : null}
                </div>

                <div className="rounded-lg border border-[#dfe5d8] bg-white p-6 shadow-sm">
                    <div className="flex items-center gap-3">
                        <div className="rounded-md bg-[#eff5ed] p-2 text-[#0f766e]">
                            <ClipboardCheck className="h-5 w-5" />
                        </div>
                        <div>
                            <h2 className="text-lg font-semibold text-[#17211f]">Termo de abertura</h2>
                            <p className="text-sm text-[#53635e]">
                                Registre os elementos iniciais que autorizam e orientam o projeto.
                            </p>
                        </div>
                    </div>

                    <form className="mt-6 space-y-5" onSubmit={enviarFormulario}>
                        <CampoTextoArea
                            erro={form.errors.objetivo}
                            id="objetivo"
                            label="Objetivo"
                            onChange={(valor) => form.setData('objetivo', valor)}
                            placeholder="Qual resultado o projeto pretende alcancar?"
                            value={form.data.objetivo}
                        />
                        <CampoTextoArea
                            erro={form.errors.justificativa}
                            id="justificativa"
                            label="Justificativa"
                            onChange={(valor) => form.setData('justificativa', valor)}
                            placeholder="Por que este projeto deve existir?"
                            value={form.data.justificativa}
                        />
                        <CampoTextoArea
                            erro={form.errors.restricoes}
                            id="restricoes"
                            label="Restricoes"
                            onChange={(valor) => form.setData('restricoes', valor)}
                            placeholder="Limites de prazo, custo, escopo, tecnologias ou recursos."
                            value={form.data.restricoes}
                        />
                        <CampoTextoArea
                            erro={form.errors.premissas}
                            id="premissas"
                            label="Premissas"
                            onChange={(valor) => form.setData('premissas', valor)}
                            placeholder="Condicoes assumidas como verdadeiras para planejar o projeto."
                            value={form.data.premissas}
                        />
                        <CampoTextoArea
                            erro={form.errors.entregas_esperadas}
                            id="entregas_esperadas"
                            label="Entregas esperadas"
                            onChange={(valor) => form.setData('entregas_esperadas', valor)}
                            placeholder="Produtos, servicos ou resultados esperados ao final."
                            value={form.data.entregas_esperadas}
                        />

                        <Button disabled={form.processing} type="submit">
                            <Save className="h-4 w-4" />
                            Salvar termo
                        </Button>
                    </form>
                </div>
            </section>
        </AppLayout>
    );
}

function Resumo({
    icon: Icon,
    rotulo,
    valor,
}: {
    icon: typeof ClipboardCheck;
    rotulo: string;
    valor: string;
}) {
    return (
        <div className="rounded-lg border border-[#dfe5d8] bg-white p-5 shadow-sm">
            <Icon className="h-5 w-5 text-[#0f766e]" />
            <p className="mt-4 text-sm text-[#66756f]">{rotulo}</p>
            <p className="mt-1 font-semibold text-[#17211f]">{valor}</p>
        </div>
    );
}

function CampoTextoArea({
    erro,
    id,
    label,
    onChange,
    placeholder,
    value,
}: {
    erro?: string;
    id: keyof TermoForm;
    label: string;
    onChange: (valor: string) => void;
    placeholder: string;
    value: string;
}) {
    return (
        <div>
            <label className="text-sm font-medium text-[#51605c]" htmlFor={id}>
                {label}
            </label>
            <textarea
                className="mt-1 min-h-24 w-full rounded-md border border-[#b9c4b7] px-3 py-2 text-sm text-[#17211f] outline-none transition focus:border-[#0f766e] focus:ring-2 focus:ring-[#d9e2d7]"
                id={id}
                onChange={(event) => onChange(event.target.value)}
                placeholder={placeholder}
                value={value}
            />
            {erro ? <p className="mt-1 text-sm text-red-600">{erro}</p> : null}
        </div>
    );
}
