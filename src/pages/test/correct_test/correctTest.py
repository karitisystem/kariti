try:
    from util import *
except Exception as e:
    print(f'Error: {e}---')
import sys

def main():
    try:
        ######################################################################
        # TRAZ OS DADOS DA PROVA DE UM ALUNO
        ######################################################################
        #img = cv2.imread('provas_scan/prt_2/prof/prova2e.png')
        img = cv2.imread(sys.argv[1])
        #Se não forem encontrados quatro triângulos 'homogêneos', retorna None
        a = paper90(img)
        #print(getInfoTriang(img)[0])
        #Se for None, adiciona a err o erro correspondente
        dados = leQr(a)
        #Se código for None, adiciona a err o erro cosrrespondente
        #Número de questões, número de alternativas e gabarito
        _, gabarito, n_qDB, n_altDB = obterProva(dados[0], dados[1])

        n, alt, _ = getOurSqr(a)
        if (len(n) != n_qDB) or (len(alt) != n_altDB):
            raise Exception('THERE ARE MORE OR LESS ALTERNATIVES OR QUESTIONS GOTTEN')

        gabarito_aluno = getAnswers(a)

        notas, answ_clear, warning = getGrades(gabarito, gabarito_aluno)
        print(f'id_prova:{dados[0]}')
        print(f'id_aluno:{dados[1]}')

        '''
        print(f'gabarito:', end='')
        for i in range(len(gabarito)):
            print(gabarito[i][1], end='')
            if i != len(gabarito) - 1:
                print(',',end='')
        '''

        print(f'resposta:', end='')
        for i in range(len(answ_clear)):
            print(answ_clear[i], end='')
            if i != len(answ_clear) - 1:
                print(',',end='')
        '''
        print(f'\npontos:', end='')
        for i in range(len(notas)):
            print(notas[i], end='')
            if i != len(notas) - 1:
                print(',',end='')
        print()
        '''

        # if warning != []:
        #     for i in warning:
        #         print(f'warn:{i}')


        #########################################################################
    except Exception as e:
        print(e)


main()
