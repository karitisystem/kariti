#-*- coding: utf-8 -*-


try:
    import sqlite3
    import os
    from util import *
except Exception as e:
    print(e)


def start_db():
    '''Trecho que inicia o banco'''
    try:
        CURRENT_FOLDER = os.path.dirname(__file__)
        nome_banco = os.path.realpath(f'{CURRENT_FOLDER}/../database/database.db')
        banco = sqlite3.connect(nome_banco)
        return banco.cursor()
    except Exception as error:
        print(f'[ERRO] NÃO FOI POSSÍVEL INICIAR O BD -> {error} <-')



def dadosProva(id_prova, basedir = '', id_alunos = None):
    """
    Dado id de uma prova, a função retorna:
        *O nome desse prova
        *O nome do professor relacionado à essa prova
        *O nome da turma relacionada à essa prova
        *A data de realização dessa provas
        *A quantidade de questões nessa prova
        *A quantidade de alternativas nessa prova
        *O nome dos alunos que estão relacionados a essa prova
        *O id de cada aluno
        *O id do usuário que está gerando a prova


    @parâmetro id_prova Identificador da prova a ser gerada
    @parâmetro basedir Local onde será salvo o arquivo, caso não seja informado
    esse campo receberá <vazio>
    @parâmetro id_alunos Id do aluno ao qual de qual a prova será gerada
        (a)None: Serão passados os dados de todos os alunos relacionados àquela
        prova
        (b)Se for passada uma lista com apenas um valor inteiro, serão passados
        os dados apenas do aluno com o id correspondente
        (c)Se for passada uma lista com mais de um valor inteiro, serão passados
        os dados de todos os alunos relacionados aos ids correspondentes

    """


    try:
        cursor = start_db()
        #Seleciona tudo de uma linha de prova
        cursor.execute('SELECT * FROM prova WHERE id_prova=?', [id_prova])
        dados_prova = cursor.fetchall()
        #Seleciona tudo de uma linha de turma
        cursor.execute(f'SELECT * FROM turma WHERE id_turma=?', [dados_prova[0][5]])
        dados_turma = cursor.fetchall()
        #Seleciona o nome de uma linha de usuario
        cursor.execute(f'SELECT nome, id_usuario FROM usuario WHERE id_usuario=?', [dados_turma[0][3]])
        dados_usuario = cursor.fetchall()
        #Seleciona id_aluno de uma linha de turma (porque uma turma toda vai estar relacionada a uma prova)
        cursor.execute(f'SELECT id_aluno FROM aluno_turma WHERE id_turma=?',[dados_prova[0][5]])
        id_aluno_prova = cursor.fetchall()
        #Seleciona peso de uma linha de gabarito
        cursor.execute(f'SELECT peso FROM gabarito WHERE id_prova=?',[id_prova])
        pesos_gabarito = cursor.fetchall()
        nome_prova = dados_prova[0][1]
        nome_professor = dados_usuario[0][0]
        id_usuario = dados_usuario[0][1]
        nome_turma = dados_turma[0][1]
        data = dados_prova[0][2]
        qtd_q = dados_prova[0][3]
        qtd_a = dados_prova[0][4]
        #Calculando a nota total da prova
        nota_prova = 0
        for i in range(len(pesos_gabarito)):
            nota_prova += pesos_gabarito[i][0]


        #Trecho que verifica quais alunos irão aparecer
        #Se None == Todos
        #Se type is int vai receber um aluno específico
        #Se type is list vai receber determinados alunos
        if id_alunos == None:
            id =  idRepetido(id_aluno_prova)


        else:
            id = []
            if type(id_alunos) is int:
                id.append(id_alunos)
            else:
                for i in id_alunos:
                    id.append(i)

        #Lista que vai receber nomes de alunos pura com 3 dimensoes
        lista_alunos = []
        #Lista que vai receber nomes de alunos com 1 dimensão
        nome_alunos = []
        for i in id:
            cursor.execute(f'SELECT nome_aluno FROM aluno WHERE id_aluno=?',[i])
            lista_alunos.append(cursor.fetchall())
        for i in range(len(id)):
                nome_alunos.append(lista_alunos[i][0][0])

        #print(nome_alunos)
        return nome_prova, nome_professor, nome_turma, data, qtd_q, qtd_a, nota_prova, nome_alunos, id, id_usuario

    except sqlite3 as error:
        print(f'[ERRO] NÃO FOI POSSÍVEL EXIBIR PROFESSORES CADASTRADOS -> {error} <-')

def obterProva(id_prova, id_aluno):
    """
    Dado id de uma prova e de um aluno, a função retorna:
        *O gabarito dessa prova
        *As respostas preenchidas nessa prova

    @parâmetro id_prova Identificador da prova a ser buscada
    @parâmetro id_aluno Identificador do aluno a ser buscado
    """
    #Número de questões, número de alternativas e gabarito
    if id_aluno != None:
        try:
            cursor = start_db()
            cursor.execute('SELECT n_questao, opcao FROM aluno_prova WHERE id_prova=? AND id_aluno=?', [id_prova, id_aluno])
            prova = cursor.fetchall()

            cursor.execute('SELECT n_questao, opcao, peso FROM gabarito WHERE id_prova=?', [id_prova])
            gabarito = cursor.fetchall()

            #Eu estou usando o "[0][0] no final, pra ele retorna, por exemplo, apenas, 4 e não [(4,)]"
            #Isso parece válido, já que sempre teremos um valor para cada
            cursor.execute('SELECT n_questoes FROM prova WHERE id_prova=?', [id_prova])
            n_q = cursor.fetchall()[0][0]

            cursor.execute('SELECT n_alternativas FROM prova WHERE id_prova=?', [id_prova])
            n_alt = cursor.fetchall()[0][0]

        except sqlite3 as error:
            print(f'[ERRO] NÃO FOI POSSÍVEL LOCALIZAR A PROVA -> {error} <-')

    return prova, gabarito, n_q, n_alt

def obterQuestoesProva(id_prova, id_aluno):
    """
    Dado id de uma prova e de um aluno, a função retorna:
        *O gabarito dessa prova
        *As respostas preenchidas nessa prova

    @parâmetro id_prova Identificador da prova a ser buscada
    @parâmetro id_aluno Identificador do aluno a ser buscado
    """
    #Número de questões, número de alternativas e gabarito
    if id_aluno != None:
        try:
            cursor = start_db()
            #Eu estou usando o "[0][0] no final, pra ele retorna, por exemplo, apenas, 4 e não [(4,)]"
            #Isso parece válido, já que sempre teremos um valor para cada
            cursor.execute('SELECT n_questoes FROM prova WHERE id_prova=?', [id_prova])
            n_q = cursor.fetchall()[0][0]

            cursor.execute('SELECT n_alternativas FROM prova WHERE id_prova=?', [id_prova])
            n_alt = cursor.fetchall()[0][0]

        except sqlite3 as error:
            print(f'[ERRO] NÃO FOI POSSÍVEL LOCALIZAR A PROVA -> {error} <-')

    return n_q, n_alt
