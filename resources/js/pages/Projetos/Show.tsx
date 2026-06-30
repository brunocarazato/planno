import { Head, Link, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    Check,
    CheckCircle2,
    Circle,
    ClipboardCheck,
    Compass,
    FileText,
    PencilLine,
    Save,
    School,
    Target,
    UserRound,
} from 'lucide-react';
import { FormEvent, useEffect, useState } from 'react';
import { createPortal } from 'react-dom';

import { AppLayout } from '../../shared/layouts/AppLayout';
import { Button, buttonVariants } from '../../shared/ui/button';
import { Dialog } from '../../shared/ui/dialog';
import { RichTextEditor } from '../../shared/ui/rich-text-editor';
import { Select } from '../../shared/ui/select';

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

type PreenchimentoForm = {
    nome: string;
    descricao: string;
    responsavel_id?: string;
    objetivo: string;
    justificativa: string;
    restricoes: string;
    premissas: string;
    entregas_esperadas: string;
};

type AtividadeDaTrilha = {
    chave: string;
    titulo: string;
    descricao: string;
    artefato: string;
    concluida: boolean;
    concluidaEm: string | null;
    concluidaPor: string | null;
};

type GrupoDaTrilha = {
    chave: string;
    nome: string;
    descricao: string;
    atividades: AtividadeDaTrilha[];
    progresso: Progresso;
};

type Progresso = {
    concluidas: number;
    total: number;
    percentual: number;
};

type Trilha = {
    id: number;
    grupos: GrupoDaTrilha[];
    progresso: Progresso;
};

type ProjetosShowProps = {
    projeto: Projeto;
    trilha: Trilha;
    podeAlterarResponsavel: boolean;
    responsaveisDisponiveis: UsuarioOpcao[];
    flash?: {
        success?: string | null;
    };
};

export default function ProjetosShow({
    projeto,
    trilha,
    podeAlterarResponsavel,
    responsaveisDisponiveis,
    flash,
}: ProjetosShowProps) {
    const preenchimentoForm = useForm<PreenchimentoForm>({
        nome: projeto.nome,
        descricao: projeto.descricao ?? '',
        ...(podeAlterarResponsavel
            ? {
                  responsavel_id: projeto.responsavel.id ? String(projeto.responsavel.id) : '',
              }
            : {}),
        objetivo: projeto.termoDeAbertura.objetivo ?? '',
        justificativa: projeto.termoDeAbertura.justificativa ?? '',
        restricoes: projeto.termoDeAbertura.restricoes ?? '',
        premissas: projeto.termoDeAbertura.premissas ?? '',
        entregas_esperadas: projeto.termoDeAbertura.entregasEsperadas ?? '',
    });
    const [trilhaAberta, setTrilhaAberta] = useState(false);
    const [atividadeEmAtualizacao, setAtividadeEmAtualizacao] = useState<string | null>(null);
    const [paginaMontada, setPaginaMontada] = useState(false);

    useEffect(() => setPaginaMontada(true), []);

    function salvarAlteracoes(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        preenchimentoForm.put(`/projetos/${projeto.id}`, {
            preserveScroll: true,
            onSuccess: () => preenchimentoForm.setDefaults(),
        });
    }

    function alternarConclusao(atividade: AtividadeDaTrilha) {
        setAtividadeEmAtualizacao(atividade.chave);

        router.patch(
            `/projetos/${projeto.id}/trilha/atividades/${atividade.chave}`,
            { concluida: !atividade.concluida },
            {
                preserveScroll: true,
                onFinish: () => setAtividadeEmAtualizacao(null),
            },
        );
    }

    const nomeResponsavel = projeto.responsavel.name ?? 'Não definido';

    return (
        <AppLayout titulo={projeto.nome} subtitulo="Detalhe do projeto didático e termo de abertura inicial.">
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

            <section className="grid grid-cols-2 gap-4 md:grid-cols-[repeat(4,minmax(0,1fr))_5.5rem]">
                <Resumo icon={ClipboardCheck} rotulo="Código" valor={projeto.codigo} />
                <Resumo icon={School} rotulo="Turma" valor={`${projeto.turma.nome} (${projeto.turma.codigo})`} />
                <Resumo icon={UserRound} rotulo="Responsável" valor={nomeResponsavel} />
                <Resumo icon={Target} rotulo="Situação" valor={projeto.situacaoFormatada} />
                <button
                    aria-label={`Abrir trilha dos grupos de processos. Progresso atual: ${trilha.progresso.percentual}%`}
                    className="journey-hint group relative flex min-h-28 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-lg border border-[#bfd1c4] bg-[#173c38] px-3 py-4 text-center text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#0f766e]"
                    onClick={() => setTrilhaAberta(true)}
                    title="Abrir trilha dos grupos de processos"
                    type="button"
                >
                    <span className="journey-hint__halo absolute h-16 w-16 rounded-full border border-[#78c9b9]/20" />
                    <Compass className="relative h-6 w-6 text-[#9ed9cd] transition-transform duration-300 group-hover:rotate-12 group-hover:scale-110" />
                    <span className="relative mt-2 text-[0.65rem] font-semibold uppercase tracking-[0.14em] text-[#c8ddd8]">
                        Trilha
                    </span>
                    <span className="relative mt-0.5 text-xs font-semibold tabular-nums text-white">
                        {trilha.progresso.percentual}%
                    </span>
                </button>
            </section>

            <form className="mt-8 pb-36 sm:pb-24" id="form-preenchimento-projeto" onSubmit={salvarAlteracoes}>
                <section className="grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
                    <div className="rounded-lg border border-[#dfe5d8] bg-white p-6 shadow-sm">
                        <div className="flex items-center gap-3">
                            <div className="rounded-md bg-[#eff5ed] p-2 text-[#0f766e]">
                                <PencilLine className="h-5 w-5" />
                            </div>
                            <div>
                                <h2 className="text-lg font-semibold text-[#17211f]">Dados do projeto</h2>
                                <p className="text-sm text-[#53635e]">
                                    Ajuste a identificação sem alterar seu código ou turma.
                                </p>
                            </div>
                        </div>

                        <div className="mt-6 space-y-4">
                            <div>
                                <label className="text-sm font-medium text-[#51605c]" htmlFor="nome-projeto">
                                    Nome
                                </label>
                                <input
                                    className="mt-1 w-full rounded-md border border-[#b9c4b7] px-3 py-2 text-sm text-[#17211f] outline-none transition focus:border-[#0f766e] focus:ring-2 focus:ring-[#d9e2d7]"
                                    id="nome-projeto"
                                    onChange={(event) => preenchimentoForm.setData('nome', event.target.value)}
                                    value={preenchimentoForm.data.nome}
                                />
                                {preenchimentoForm.errors.nome ? (
                                    <p className="mt-1 text-sm text-red-600">{preenchimentoForm.errors.nome}</p>
                                ) : null}
                            </div>
                            <div>
                                <label className="text-sm font-medium text-[#51605c]" htmlFor="descricao-projeto">
                                    Descrição
                                </label>
                                <textarea
                                    className="mt-1 min-h-28 w-full rounded-md border border-[#b9c4b7] px-3 py-2 text-sm text-[#17211f] outline-none transition focus:border-[#0f766e] focus:ring-2 focus:ring-[#d9e2d7]"
                                    id="descricao-projeto"
                                    onChange={(event) => preenchimentoForm.setData('descricao', event.target.value)}
                                    placeholder="Contexto, problema ou cliente do projeto didático."
                                    value={preenchimentoForm.data.descricao}
                                />
                                {preenchimentoForm.errors.descricao ? (
                                    <p className="mt-1 text-sm text-red-600">{preenchimentoForm.errors.descricao}</p>
                                ) : null}
                            </div>
                        </div>

                        <dl className="mt-6 space-y-4 text-sm">
                            <div>
                                <dt className="font-medium text-[#51605c]">Período da turma</dt>
                                <dd className="mt-1 text-[#53635e]">
                                    {projeto.turma.periodoFormatado || 'Não informado'}
                                </dd>
                            </div>
                            <div>
                                <dt className="font-medium text-[#51605c]">Responsável</dt>
                                <dd className="mt-1 text-[#53635e]">
                                    {nomeResponsavel}
                                    {projeto.responsavel.ra ? ` (${projeto.responsavel.ra})` : null}
                                </dd>
                            </div>
                            <div>
                                <dt className="font-medium text-[#51605c]">Próxima etapa</dt>
                                <dd className="mt-1 text-[#53635e]">
                                    Avançar pelas atividades da trilha e produzir os artefatos esperados.
                                </dd>
                            </div>
                        </dl>

                        {podeAlterarResponsavel ? (
                            <div className="mt-6 border-t border-[#dfe5d8] pt-5">
                                <label className="text-sm font-medium text-[#51605c]" htmlFor="responsavel_id">
                                    Alterar responsável
                                </label>
                                <Select
                                    className="mt-1"
                                    id="responsavel_id"
                                    invalid={Boolean(preenchimentoForm.errors.responsavel_id)}
                                    onValueChange={(value) => preenchimentoForm.setData('responsavel_id', value)}
                                    options={[
                                        {
                                            label: 'Selecione um responsável',
                                            value: '',
                                        },
                                        ...responsaveisDisponiveis.map((responsavel) => ({
                                            label: `${responsavel.name}${responsavel.ra ? ` (${responsavel.ra})` : ''}`,
                                            value: String(responsavel.id),
                                        })),
                                    ]}
                                value={preenchimentoForm.data.responsavel_id ?? ''}
                                />
                                {preenchimentoForm.errors.responsavel_id ? (
                                    <p className="mt-1 text-sm text-red-600">
                                        {preenchimentoForm.errors.responsavel_id}
                                    </p>
                                ) : null}
                                <p className="mt-2 text-xs text-[#61716b]">
                                    A troca será aplicada junto com as demais alterações ao salvar.
                                </p>
                            </div>
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

                        <div className="mt-6 space-y-5">
                            <CampoTextoArea
                                erro={preenchimentoForm.errors.objetivo}
                                id="objetivo"
                                label="Objetivo"
                                onChange={(valor) => preenchimentoForm.setData('objetivo', valor)}
                                placeholder="Qual resultado o projeto pretende alcançar?"
                                value={preenchimentoForm.data.objetivo}
                            />
                            <CampoTextoArea
                                erro={preenchimentoForm.errors.justificativa}
                                id="justificativa"
                                label="Justificativa"
                                onChange={(valor) => preenchimentoForm.setData('justificativa', valor)}
                                placeholder="Por que este projeto deve existir?"
                                value={preenchimentoForm.data.justificativa}
                            />
                            <CampoTextoArea
                                erro={preenchimentoForm.errors.restricoes}
                                id="restricoes"
                                label="Restrições"
                                onChange={(valor) => preenchimentoForm.setData('restricoes', valor)}
                                placeholder="Limites de prazo, custo, escopo, tecnologias ou recursos."
                                value={preenchimentoForm.data.restricoes}
                            />
                            <CampoTextoArea
                                erro={preenchimentoForm.errors.premissas}
                                id="premissas"
                                label="Premissas"
                                onChange={(valor) => preenchimentoForm.setData('premissas', valor)}
                                placeholder="Condições assumidas como verdadeiras para planejar o projeto."
                                value={preenchimentoForm.data.premissas}
                            />
                            <CampoTextoArea
                                erro={preenchimentoForm.errors.entregas_esperadas}
                                id="entregas_esperadas"
                                label="Entregas esperadas"
                                onChange={(valor) => preenchimentoForm.setData('entregas_esperadas', valor)}
                                placeholder="Produtos, serviços ou resultados esperados ao final."
                                value={preenchimentoForm.data.entregas_esperadas}
                            />
                        </div>
                    </div>
                </section>
            </form>

            {paginaMontada
                ? createPortal(
                      <div className="fixed inset-x-4 bottom-4 z-40 mx-auto flex max-w-3xl flex-col gap-3 rounded-xl border border-[#c5d3c7] bg-[#eff5ed]/95 px-4 py-3 shadow-[0_18px_48px_-18px_rgba(23,60,56,0.55)] backdrop-blur-md sm:flex-row sm:items-center sm:justify-between">
                          <div aria-live="polite" className="flex items-center gap-3">
                              <span
                                  className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-full ${
                                      preenchimentoForm.isDirty
                                          ? 'bg-[#fff0e9] text-[#c94f2d]'
                                          : 'bg-white text-[#0f766e]'
                                  }`}
                              >
                                  {preenchimentoForm.isDirty ? (
                                      <PencilLine className="h-4 w-4" />
                                  ) : (
                                      <CheckCircle2 className="h-4 w-4" />
                                  )}
                              </span>
                              <div>
                                  <p className="text-sm font-semibold text-[#17211f]">Preenchimento do projeto</p>
                                  <p className="text-xs text-[#53635e]">
                                      {preenchimentoForm.isDirty
                                          ? 'Você tem alterações que ainda não foram salvas.'
                                          : 'Todos os dados acima estão salvos.'}
                                  </p>
                              </div>
                          </div>
                          <Button
                              className="w-full sm:w-auto"
                              disabled={preenchimentoForm.processing || !preenchimentoForm.isDirty}
                              form="form-preenchimento-projeto"
                              type="submit"
                          >
                              <Save className="h-4 w-4" />
                              {preenchimentoForm.processing ? 'Salvando...' : 'Salvar alterações'}
                          </Button>
                      </div>,
                      document.body,
                  )
                : null}

            <Dialog
                aberto={trilhaAberta}
                className="max-h-[calc(100vh-3rem)] max-w-7xl overflow-y-auto p-4 md:p-6"
                descricao="Consulte o percurso pedagógico, acompanhe o progresso e revise as atividades do projeto."
                onClose={() => setTrilhaAberta(false)}
                titulo="Trilha dos grupos de processos"
            >
            <section className="project-journey overflow-hidden rounded-xl border border-[#cad8cd] bg-[#173c38] text-white shadow-[0_24px_60px_-38px_rgba(23,60,56,0.8)]">
                <div className="relative overflow-hidden border-b border-white/10 px-6 py-7 md:px-8 md:py-9">
                    <div className="journey-grid pointer-events-none absolute inset-0 opacity-30" />
                    <div className="relative grid gap-7 lg:grid-cols-[1fr_0.7fr] lg:items-end">
                        <div>
                            <div className="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-[#9ed9cd]">
                                <Compass className="h-4 w-4" />
                                Percurso pedagógico
                            </div>
                            <h2 className="mt-3 max-w-2xl text-2xl font-semibold tracking-tight md:text-3xl">
                                {projeto.nome}
                            </h2>
                            <p className="mt-3 max-w-2xl text-sm leading-6 text-[#c8ddd8]">
                                Cada atividade conecta uma decisão do projeto ao artefato que materializa o aprendizado.
                                Conclua, revise e acompanhe o avanço sem perder a visão do todo.
                            </p>
                        </div>
                        <div className="rounded-lg border border-white/10 bg-white/[0.07] p-5 backdrop-blur-sm">
                            <div className="flex items-end justify-between gap-4">
                                <div>
                                    <p className="text-xs font-medium uppercase tracking-[0.16em] text-[#9ed9cd]">Progresso geral</p>
                                    <p className="mt-2 text-3xl font-semibold tabular-nums">{trilha.progresso.percentual}%</p>
                                </div>
                                <p className="text-sm text-[#c8ddd8]">
                                    {trilha.progresso.concluidas} de {trilha.progresso.total} atividades
                                </p>
                            </div>
                            <BarraDeProgresso percentual={trilha.progresso.percentual} />
                        </div>
                    </div>
                </div>

                <div className="grid gap-px bg-white/10 lg:grid-cols-5">
                    {trilha.grupos.map((grupo, indice) => (
                        <div className="bg-[#173c38] px-5 py-4" key={grupo.chave}>
                            <div className="flex items-center gap-3">
                                <span
                                    className={`flex h-8 w-8 shrink-0 items-center justify-center rounded-full border text-xs font-semibold ${
                                        grupo.progresso.percentual === 100
                                            ? 'border-[#78c9b9] bg-[#78c9b9] text-[#173c38]'
                                            : 'border-white/20 text-[#9ed9cd]'
                                    }`}
                                >
                                    {grupo.progresso.percentual === 100 ? <Check className="h-4 w-4" /> : indice + 1}
                                </span>
                                <div className="min-w-0">
                                    <p className="truncate text-sm font-medium">{grupo.nome}</p>
                                    <p className="mt-0.5 text-xs text-[#9ebbb5]">{grupo.progresso.percentual}% concluído</p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                <div className="space-y-5 bg-[#f6f7f2] p-4 text-[#17211f] md:p-6">
                    {trilha.grupos.map((grupo, indice) => (
                        <article
                            className="journey-group overflow-hidden rounded-xl border border-[#d8e1d7] bg-white shadow-sm"
                            key={grupo.chave}
                        >
                            <div className="grid gap-5 border-b border-[#e3e9df] bg-[#fbfcf8] p-5 md:grid-cols-[auto_1fr_auto] md:items-center md:p-6">
                                <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-[#e7f0e9] font-semibold text-[#0d625c]">
                                    {String(indice + 1).padStart(2, '0')}
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold">{grupo.nome}</h3>
                                    <p className="mt-1 text-sm leading-6 text-[#61716b]">{grupo.descricao}</p>
                                </div>
                                <div className="min-w-40">
                                    <div className="flex justify-between text-xs font-medium text-[#61716b]">
                                        <span>Progresso</span>
                                        <span>{grupo.progresso.concluidas}/{grupo.progresso.total}</span>
                                    </div>
                                    <BarraDeProgresso clara percentual={grupo.progresso.percentual} />
                                </div>
                            </div>

                            <div className="divide-y divide-[#e8ece5]">
                                {grupo.atividades.map((atividade) => (
                                    <button
                                        aria-pressed={atividade.concluida}
                                        className={`journey-activity grid w-full cursor-pointer gap-4 p-5 text-left transition md:grid-cols-[auto_1fr_auto] md:items-center md:px-6 ${
                                            atividade.concluida ? 'bg-[#f1f7f1]' : 'bg-white hover:bg-[#fafbf7]'
                                        }`}
                                        disabled={atividadeEmAtualizacao === atividade.chave}
                                        key={atividade.chave}
                                        onClick={() => alternarConclusao(atividade)}
                                        type="button"
                                    >
                                        <span
                                            className={`flex h-9 w-9 items-center justify-center rounded-full border transition ${
                                                atividade.concluida
                                                    ? 'border-[#0f766e] bg-[#0f766e] text-white'
                                                    : 'border-[#b9c9be] bg-white text-[#8a9993]'
                                            }`}
                                        >
                                            {atividade.concluida ? <CheckCircle2 className="h-5 w-5" /> : <Circle className="h-5 w-5" />}
                                        </span>
                                        <span>
                                            <span className={`block font-medium ${atividade.concluida ? 'text-[#24574f]' : 'text-[#17211f]'}`}>
                                                {atividade.titulo}
                                            </span>
                                            <span className="mt-1 block text-sm leading-6 text-[#61716b]">{atividade.descricao}</span>
                                            {atividade.concluida && atividade.concluidaPor ? (
                                                <span className="mt-2 block text-xs text-[#648078]">Concluída por {atividade.concluidaPor}</span>
                                            ) : null}
                                        </span>
                                        <span className="inline-flex w-fit items-center gap-2 rounded-full border border-[#d8e1d7] bg-[#f6f7f2] px-3 py-1.5 text-xs font-medium text-[#51605c]">
                                            <FileText className="h-3.5 w-3.5" />
                                            {atividade.artefato}
                                        </span>
                                    </button>
                                ))}
                            </div>
                        </article>
                    ))}
                </div>
            </section>
            </Dialog>
        </AppLayout>
    );
}

function BarraDeProgresso({ clara = false, percentual }: { clara?: boolean; percentual: number }) {
    return (
        <div className={`mt-3 h-2 overflow-hidden rounded-full ${clara ? 'bg-[#dfe8df]' : 'bg-black/20'}`}>
            <div
                className={`h-full rounded-full transition-[width] duration-500 ${clara ? 'bg-[#0f766e]' : 'bg-[#78c9b9]'}`}
                style={{ width: `${percentual}%` }}
            />
        </div>
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
    id: keyof PreenchimentoForm;
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
            <RichTextEditor
                id={id}
                invalid={Boolean(erro)}
                onChange={onChange}
                placeholder={placeholder}
                value={value}
            />
            {erro ? <p className="mt-1 text-sm text-red-600">{erro}</p> : null}
        </div>
    );
}
