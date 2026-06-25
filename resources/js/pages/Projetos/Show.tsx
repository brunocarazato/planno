import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, ClipboardCheck, Save, School, Target } from 'lucide-react';
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
    };
    termoDeAbertura: {
        objetivo: string | null;
        justificativa: string | null;
        restricoes: string | null;
        premissas: string | null;
        entregasEsperadas: string | null;
    };
};

type TermoForm = {
    objetivo: string;
    justificativa: string;
    restricoes: string;
    premissas: string;
    entregas_esperadas: string;
};

type ProjetosShowProps = {
    projeto: Projeto;
    flash?: {
        success?: string | null;
    };
};

export default function ProjetosShow({ projeto, flash }: ProjetosShowProps) {
    const form = useForm<TermoForm>({
        objetivo: projeto.termoDeAbertura.objetivo ?? '',
        justificativa: projeto.termoDeAbertura.justificativa ?? '',
        restricoes: projeto.termoDeAbertura.restricoes ?? '',
        premissas: projeto.termoDeAbertura.premissas ?? '',
        entregas_esperadas: projeto.termoDeAbertura.entregasEsperadas ?? '',
    });

    function enviarFormulario(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        form.put(`/projetos/${projeto.id}/termo-de-abertura`, {
            preserveScroll: true,
        });
    }

    return (
        <AppLayout titulo={projeto.nome} subtitulo="Detalhe do projeto didatico e termo de abertura inicial.">
            <Head title={projeto.nome} />

            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <Link className={buttonVariants({ variant: 'secondary' })} href="/projetos">
                    <ArrowLeft className="h-4 w-4" />
                    Voltar para projetos
                </Link>
                <span className="rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1 text-sm font-medium text-cyan-800">
                    {projeto.situacaoFormatada}
                </span>
            </div>

            {flash?.success ? (
                <div className="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {flash.success}
                </div>
            ) : null}

            <section className="grid gap-4 md:grid-cols-3">
                <Resumo icon={ClipboardCheck} rotulo="Codigo" valor={projeto.codigo} />
                <Resumo icon={School} rotulo="Turma" valor={`${projeto.turma.nome} (${projeto.turma.codigo})`} />
                <Resumo icon={Target} rotulo="Situacao" valor={projeto.situacaoFormatada} />
            </section>

            <section className="mt-8 grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
                <div className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-semibold text-slate-950">Resumo do projeto</h2>
                    <p className="mt-3 text-sm leading-6 text-slate-600">
                        {projeto.descricao || 'Descricao ainda nao informada para este projeto.'}
                    </p>
                    <dl className="mt-6 space-y-4 text-sm">
                        <div>
                            <dt className="font-medium text-slate-700">Periodo da turma</dt>
                            <dd className="mt-1 text-slate-600">{projeto.turma.periodo || 'Nao informado'}</dd>
                        </div>
                        <div>
                            <dt className="font-medium text-slate-700">Proxima etapa</dt>
                            <dd className="mt-1 text-slate-600">
                                Completar o termo de abertura para seguir para a trilha dos grupos de processos.
                            </dd>
                        </div>
                    </dl>
                </div>

                <div className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex items-center gap-3">
                        <div className="rounded-md bg-cyan-50 p-2 text-cyan-700">
                            <ClipboardCheck className="h-5 w-5" />
                        </div>
                        <div>
                            <h2 className="text-lg font-semibold text-slate-950">Termo de abertura</h2>
                            <p className="text-sm text-slate-600">
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
        <div className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <Icon className="h-5 w-5 text-cyan-700" />
            <p className="mt-4 text-sm text-slate-500">{rotulo}</p>
            <p className="mt-1 font-semibold text-slate-950">{valor}</p>
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
            <label className="text-sm font-medium text-slate-700" htmlFor={id}>
                {label}
            </label>
            <textarea
                className="mt-1 min-h-24 w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-900 outline-none transition focus:border-cyan-700 focus:ring-2 focus:ring-cyan-100"
                id={id}
                onChange={(event) => onChange(event.target.value)}
                placeholder={placeholder}
                value={value}
            />
            {erro ? <p className="mt-1 text-sm text-red-600">{erro}</p> : null}
        </div>
    );
}
