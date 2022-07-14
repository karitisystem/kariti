try:
    import cv2
    import numpy as np
    import os
    import os.path
    import sys
    from fpdf import FPDF
    from PIL import ImageFont, ImageDraw, Image
    #from util import * Já está sendo inportada em dbProva.py
    from libQr import *
    from dbProva import *
except Exception as e:
    print(e)

def gerar_prova(id_prova, basedir ='.', id_alunos = None):
    #Quando eu comecei a trabalhar no terminal do Ubuntu ele dizia que essa variável não havia sigo criada, então eu a criei aqui
    path = ''
    try:
        #Verifica se a pasta 'provas' já existe
        #Se não existir, ela será criada
        if(not os.path.isdir(f'{basedir}/')):
            os.mkdir(f'{basedir}/')

        #nome_prova, nome_professor, nome_turma, data, qtd_q, qtd_a, nome_alunos
        prova, prof, turma, data, qtd_quadrado_v, qtd_quadrado_h, nota_prova, aluno, id_aluno, id_usuario = dadosProva(id_prova, basedir, id_alunos)

        pdf = FPDF(format = (int(1240/2.7),int(1754/2.7))) #Página definda para o tamanho da imagem
        for m in range(len(aluno)):
            #Pega a prova E gabarito pra mostrar o que o aluno marcou e onde ele acertou
            respostas, gabarito, _, _ = obterProva(str(id_prova), str(id_aluno[m]))
            if respostas != []:
                img = np.ones((1754,1240,3),np.uint8)*255 #imagem 1754x1240, com fundo branco e 3 canais para as cores

                altura = img.shape[0]
                largura = img.shape[1]

                fonte = cv2.FONT_HERSHEY_SIMPLEX
                escala = 0.7
                espessura = 2

                #Desenha linha e
                img = cv2.line(img,(160,270),(largura-160,270),(0,0,0),1)
                #Segunda linha
                img = cv2.line(img,(80,560),(largura-80,560),(0,0,0),2)
                #Desenha retângulos das notas
                cv2.rectangle(img, (330, 320), (580, 470), (0, 0, 0), 2)
                cv2.rectangle(img, (largura-330, 320), (largura-580, 470), (0, 0, 0), 2)

                #Desenha as informações do cabecalho
                img = desenhaCabecalho(img, largura, altura, aluno[m], prof, prova, turma, data, nota_prova)

                #Gera o qr
                #Nº da prova . Nº do aluno
                msg = formataQr(f'{id_prova}.{id_aluno[m]}')
                qr_code = escreveQr(msg)

                #Coloca o qr na imagem
                img[0 : qr_code.shape[0], largura-qr_code.shape[1] : largura] = qr_code

                #Apaga qrcode depois de adicioná-lo ao pdf
                os.unlink(f'qr.png')

                #Valors originais dos topos dos triângulos (último campo, posição x): 40,largura-70, 40,
                #Cria marcadores triângulo
                t1 = np.array([[40,640],[70,640],[40,610]], np.int32)
                t2 = np.array([[largura-70,640],[largura-40,640],[largura-70,610]], np.int32)
                t3 = np.array([[40,altura-60],[70,altura-60],[40,altura-90]], np.int32)
                t4 = np.array([[largura-70,altura-60],[largura-40,altura-60],[largura-70,altura-90]], np.int32)
                t = [t1, t2, t3, t4]
                for i in range(len(t)):
                    cv2.fillPoly(img, [t[i]], (0, 0, 0))

                #Coloca os marcadores quadrados na vertical
                espaco = int(0)
                cinza = (210, 210, 210)
                vermelho = (0, 0, 255)
                verde = (75, 255, 75)
                for i in range(qtd_quadrado_v):
                    cv2.rectangle(img, (120,700 + espaco), (140, 720 + espaco), (0, 0, 0), -1)
                    cv2.putText(img, f'{i+1}', (150,720 + espaco), fonte, escala, cinza, espessura)
                    espaco += 45

                #Coloca os marcadores quadrados na horizontal
                espaco = 0
                for i in range(qtd_quadrado_h):
                    cv2.rectangle(img, (260 + espaco,650), (280 + espaco, 670), (0, 0, 0), -1)
                    espaco += 120


                #Colocando os círculos e letras
                letras = ['A','B','C','D','E','F','G']
                espaco_x = espaco_y = 0
                nota_aluno = 0
                for i in range(qtd_quadrado_v):
                    #Calcula nota do aluno
                    if(respostas[i][1] == gabarito[i][1]):
                        nota_aluno += gabarito[i][2]
                    for j in range(qtd_quadrado_h):
                        cv2.circle(img, (270 + espaco_x, 710 + espaco_y), 14, cinza,2)
                        if (respostas[i][0] == i+1 and respostas[i][1] == j+1):
                            # Just if there is a  answer in this question
                            if (respostas[i][1] != -1):
                                # Draw red circle
                                cv2.circle(img, (270 + espaco_x, 710 + espaco_y), 14, vermelho,-1)
                                cv2.circle(img, (270 + espaco_x, 710 + espaco_y), 14, vermelho,2)

                        if (gabarito[i][0] == i+1 and gabarito[i][1] == j+1):
                            # Just if there is a  answer in this question
                            if(respostas[i][1] != -1):
                                # Draw green circle
                                cv2.circle(img, (270 + espaco_x, 710 + espaco_y), 14, verde,-1)
                                cv2.circle(img, (270 + espaco_x, 710 + espaco_y), 14, verde,2)
                        cv2.putText(img, f'{letras[j]}', (263 + espaco_x, 717 + espaco_y), fonte, escala, cinza, espessura)
                        espaco_x += 120
                    espaco_x = 0
                    espaco_y += 45

                # print(respostas)

                #Coloca os campos de nota obtida pelo aluno
                img_pil = Image.fromarray(img) #Não sei o que faz
                draw = ImageDraw.Draw(img_pil) #Não sei o que faz

                font2 = ImageFont.truetype(fm.findfont(fm.FontProperties(family='DejaVu Sans')),72)
                peso_total_aluno = float(nota_aluno)#Peso total da prova
                if peso_total_aluno >= 100:
                    draw.text((380 + 280,355), f'{peso_total_aluno:.2f}', font = font2, fill = (0, 0, 0, 0))
                elif peso_total_aluno >= 10:
                    draw.text((405 + 280,355), f'{peso_total_aluno:.2f}', font = font2, fill = (0, 0, 0, 0))
                else:
                    draw.text((425 + 280,355), f'{peso_total_aluno:.2f}', font = font2, fill = (0, 0, 0, 0))

                img = np.array(img_pil)

                #Redminsionando a imagem temporiamente apenas para os teste
                #img_new_h = resizeImg(img, 840)

                #cv2.imshow("Canvas", img)
                #cv2.waitKey(0)
                cv2.imwrite(f'{basedir}/prova{m}.png', img)

                #Cria nova página, quebra a página, busca a imagem
                pdf.add_page()
                pdf.set_auto_page_break(0)
                pdf.image(f'{basedir}/prova{m}.png')

                #Apaga imagem depois de adicioná-la ao pdf
                os.unlink(f'{basedir}/prova{m}.png')

        pdf.output(f'{basedir}/prova{id_usuario}.pdf', "F")
        #COMANDO QUE BAIXA O PDF#
        #COMANDO QUE EXCLUI O PDF#
        path = os.path.abspath(f'{basedir}/prova{id_usuario}.pdf')

    except Exception as e:
        print(e)

    return path.replace("\\", "/")

#python[-1] file.py[0] id_prova[1] id_alunos[2]
try:

    id_prova = sys.argv[1]
    id_alunos = sys.argv[2].split(',')

    path = gerar_prova(id_prova = id_prova, id_alunos = id_alunos)
    print(path)
except Exception as e:
    print(e)
