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
        img = cv2.imread(sys.argv[1])

        # Se não forem encontrados quatro triângulos 'homogêneos', retorna None
        a = paper90(img)

        # Se for None, adiciona a err o erro correspondente
        qr_code_info = leQr(a)

        if not qr_code_info:
            raise KeyError('Não foi possível localizar as informações no QRCode.')

        # Se código for None, adiciona a err o erro cosrrespondente
        # Número de questões, número de alternativas e gabarito
        _, gabarito, num_questions_DB, num_alternatives_DB = obterProva(
            id_aluno=qr_code_info["id_aluno"],
            id_prova=qr_code_info["id_prova"],
        )

        question_squares, alternative_squares, _ = getOurSqr(a)
        if (
            len(question_squares) != num_questions_DB) or\
            (len(alternative_squares) != num_alternatives_DB
        ):
            raise Exception('THERE ARE MORE OR LESS ALTERNATIVES OR QUESTIONS GOTTEN')
        gabarito_aluno = getAnswers(a)

        _, answ_clear, _ = getGrades(gabarito, gabarito_aluno)
        print(f'id_prova:{qr_code_info["id_prova"]}')
        print(f'id_aluno:{qr_code_info["id_aluno"]}')
        print(f'resposta:{",".join([str(answer) for answer in answ_clear])}')

        #########################################################################
    except Exception as e:
        print(e)


main()
