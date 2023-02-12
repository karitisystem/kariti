#!/usr/bin/python3.8
import cv2
import numpy as np
import math
import os
from PIL import ImageFont, ImageDraw, Image
from util import *
from libQr import *
from dbProva import *
#A linha abaixo define um novo caminho para o metaploid usar, já que o original não pode ser escrito
if(not os.path.isdir(f'mat_cache')):
    os.mkdir(f'mat_cache')
os.environ['MPLCONFIGDIR'] = "mat_cache"
import matplotlib.font_manager as fm # to create font

def resizeImg(img, w):
    """
    Essa função retorna uma imagem com o tamanho (em pixels) alterada, em
    seguida, essa nova imagem é retornada.

    @parâmetro img Arquivo de imagem que vai ter o tamanho alterado
    @parâmetro w nova largura do arquivo de imagem
    """
    im_high = img.shape[0]
    im_width = img.shape[1]
    proportion = float(max(im_high, im_width)/min(im_high, im_width))
    if im_high > im_width:
        im_width_n = w
        im_high_n = int(im_width_n * proportion)
        img = cv2.resize(img, (im_width_n, im_high_n), interpolation=cv2.INTER_AREA)
    else:
        im_high_n = w
        im_width_n = int(im_high_n * proportion)
        img = cv2.resize(img, (im_width_n, im_high_n), interpolation=cv2.INTER_AREA)
    return img



def storeImg(*img):
    """
    Essa função armazena o disco rígido as imagens que são passadas como lista.

    @parâmetro img Lista que armazena os arquivos que serão escritors no disco
    rígido
    """
    #names = [im_color, im_gray, im_bw, im_color_guide]
    for i in range(len(img)):
        cv2.imwrite(f"{i}.jpg", img[i])



def desenhaCabecalho(img, largura, altura, aluno, prof, prova, turma, data, nota_prova):
    """
    Essa função desenha o cabeçalho da prova no canvas passado como imagem, em
    seguida essa imagem com as informações é retornada.

    @parâmetro img Arquivo de imagem (imagem em branco) que vai receber as
    informações em texto
    @parâmetro Largura do imagem que está sendo passada
    @parâmetro Altura do imagem que está sendo passada
    @parâmetro Nome completo do aluno dessa prova
    @parâmetro Nome do professor
    @parâmetro Nome da prova
    @parâmetro Nome da Turma
    @parâmetro Data de realização da prova
    """



    # Desenha os textos do cabeçalho
    cabecalho_info = [f'Aluno(a): {aluno}', f'Professor(a): {prof}', f'Prova: {prova}', f'Turma: {turma}', f'Data: {data.split("-")[2]} / {data.split("-")[1]} / {data.split("-")[0]}']
    #MUUUUUUUUUUUUUUUUUUITA ATENÇÃO Aqui
    font = ImageFont.truetype(fm.findfont(fm.FontProperties(family='DejaVu Sans')),26)
    #Essa fonte dá problema no ubuntu
    #font = ImageFont.truetype("arial.ttf", 26) #Define a fonte que será usada
    img_pil = Image.fromarray(img) #Não sei o que faz
    draw = ImageDraw.Draw(img_pil) #Não sei o que faz
    espaco = int(0)
    c = 1 #contador pra evitar de repetir a última ocorrência com o for
    for i in range(len(cabecalho_info)):
        if c < len(cabecalho_info):
            draw.text((40, 40 + espaco),  cabecalho_info[i], font = font, fill = (0, 0, 0, 0))
            espaco += 30
        c += 1
    #Desenha "Nome do aluno" embaixo da primeira linha
    #draw.text((558,275),  f'Nome do Aluno', font = ImageFont.truetype("arial.ttf", 16), fill = (0, 0, 0, 0)) #Windows
    draw.text((558,275),  f'Nome do Aluno', font = ImageFont.truetype(fm.findfont(fm.FontProperties(family='DejaVu Sans')),16), fill = (0, 0, 0, 0)) #Ubuntu
    draw.text((40, 40 + espaco),  cabecalho_info[i], font = font, fill = (0, 0, 0, 0))

    font2 = ImageFont.truetype(fm.findfont(fm.FontProperties(family='DejaVu Sans')),72)
    #Coloca os campos de nota
    peso_total = float(nota_prova)#Peso total da prova
    if peso_total >= 100:
        draw.text((340,355), f'{peso_total:.2f}', font = font2, fill = (0, 0, 0, 0))
    elif peso_total >= 10:
        draw.text((365,355), f'{peso_total:.2f}', font = font2, fill = (0, 0, 0, 0))
    else:
        draw.text((385,355), f'{peso_total:.2f}', font = font2, fill = (0, 0, 0, 0))

    draw.text((330,475), f'Peso total da prova', font = font, fill = (0, 0, 0, 0))
    draw.text((largura - 580,475), f'Nota do Aluno', font = font, fill = (0, 0, 0, 0))
    #Faz a imagem original receber os desenhos do método draw
    return np.array(img_pil)





def idRepetido(lista):
    """
    Essa retorna uma lista apenas ids únicos

    @parâmetro lista Lista contendo todos os id correspondentes a uma pesquisa

    """
    id = []
    for i in range(len(lista)):
        if lista[i][0] not in id:
            id.append(lista[i][0])
    return id



def getImgNoColor(img):
    """
    Dada uma imagem, essa função retorna:
    *A versão em tons de cinza dessa imagem
    *A versão em P&B dessa imagem

    @parâmetro img Arquivo de imagem

    """
    im_gray = cv2.split(cv2.cvtColor(img, cv2.COLOR_BGR2HSV))[2]
    _, im_bw = cv2.threshold(im_gray, 0, 255, cv2.THRESH_OTSU | cv2.THRESH_BINARY)

    #Debug
    #cv2.imwrite('batata.png', im_bw )

    return im_gray, im_bw

#Função que organiza todos os triângulos em uma lista mais fácil de visualizar
def formatShape(coord):
    """
    Dada uma lista contendo os triângulos obtidos por, respectivamente,
    cv2.findContours() e cv2.approxPolyDP(), o método retorna:
        *Uma lista em que cada triângulo encontrado é um elemento e cada triângulo
        é representado por um lista do tipo [[x1, y1], [x2, y2], [x3, y3]]

    @parâmetro coord Lista de triângulos

    """
    shapes_pure = coord
    shapes = []
    shape = []
    pt = []

    for i in range(len(shapes_pure)):
        for j in range(len(shapes_pure[i])):
            pt.append(shapes_pure[i][j][0][0])
            pt.append(shapes_pure[i][j][0][1])
            shape.append(pt.copy())
            pt.clear()
        shapes.append(shape.copy())
        shape.clear()
    return shapes



#Função que trunca um número real sem arredondá-lo
def trunc(num, digits=3):
    """
    Dado um número, a função vai retornar:
        *Esse número truncado com o número de casas decimais definidas em digits
        *Um valor 1 positivo, caso esse número seja positivo, ou um valor
        1 negativo, caso esse número seja segativo

    @parâmetro num Número a ser truncado
    @parâmetro digits Quantidade de casas decimais que esse número terá

    """
    num = round(num, 2)
    sinal = 1
    #Se o coeficiente angular for negativo, sinal recebe -1 para mútiplicar o valor do ângulo
    #O coefiente negativo também é mútiplicado por -1 porque ele também não pode estar negativo
    if num < 0:
        sinal *= -1
        num *= sinal
    if num != 1 or num != 0:
        num = float(num)
        i, r = str(num).split('.')

    txt = f'{i}.{r[:digits]}'
    tg = float(txt)
    return tg, sinal


#Função que transforma o coefienteangular de uma reta em graus
def TG2AN(m):
    """
    Dado um coefieciente angular real, a função vai retornar:
        *O valor correspondente em graus

    @parâmetro m Coeficiente angular

    """

    ang = math.degrees(math.atan(m))

    return ang



def ptLateral(pts_tri):
    """
    Dada uma lista do tipo [[x1, y1], [x2, y2], [x3, y3]] correspondente às
    coordenadas dos vértices de um triângulo isosceles, a função vai retornar:
        *Um lista do tipo [x, y] com as coordenadas do ponto da base à esquerda
        *Um lista do tipo [x, y] com as coordenadas do ponto da base à direita

    @parâmetro pts_tri Lista contendo as coordenadas dos vértices doo triângulo

    """
    pts_left = []
    pts_right = []
    pt_x_left = pt_x_right = 0
    v_left = v_right = 0

    #Esse trecho busca, respectivamente, o menor e o maior valor em x no triângulo
    j = 0
    for i in pts_tri:
        if j == 0:
            pt_x_left = i
            pt_x_right = i
            j += 1

        if i[0] < pt_x_left[0]:
            pt_x_left = i
        elif i[0] > pt_x_right[0]:
            pt_x_right = i
    #Esse quais pontos tem em x tem, respectivamente, o menor e o maior valor
    for i in range(len(pts_tri)):
        if pts_tri[i][0] == pt_x_left[0]:
            pts_left.append(pts_tri[i])
        elif pts_tri[i][0] == pt_x_right[0]:
            pts_right.append(pts_tri[i])
    #Esse trecho vê dentre os pontos com menor valor em x, qual tem o maior valor em y
    for i in pts_left:
        if i[1] >= pt_x_left[1]:
            pt_x_left = i
    #Esse trecho vê dentre os pontos com maior valor em x, qual tem o maior valor em y
    for i in pts_right:
        if i[1] >= pt_x_right[1]:
            pt_x_right = i

    return pt_x_left, pt_x_right



def getTriang(im_color):
    """
    Dada um imagem, o método retorna:
        *Uma lista em que cada triângulo encontrado é um elemento e cada triângulo
        é representado por um lista do tipo [[x1, y1], [x2, y2], [x3, y3]]
        *A imagem em tons de cinza da correspondente imagem de entrada
        *A imagem em P&B da correspondente imagem de entrada

    @parâmetro im_color Arquivo de imagem da qual serão extraídos os triângulos

    """

    im_gray, im_bw = getImgNoColor(im_color)
    im_color_guide = im_color.copy()

    contours, _ = cv2.findContours(cv2.threshold(im_bw, 0, 255, cv2.THRESH_BINARY_INV)[1], cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    triangles = []
    tri = []
    centers = []
    for cnt in contours:
        approx = cv2.approxPolyDP(cnt, 0.04 * cv2.arcLength(cnt, True), True)

        if len(approx) == 3:
            triangles.append(approx)
            tri.append(cnt)


    #debug
    cv2.drawContours(im_color_guide, triangles, -1, (0, 0, 255), 2)
    #cv2.imwrite('batata-assada.png', im_color_guide)

    return formatShape(triangles), im_gray, im_bw

def getInfoTriang(img):
    """
    Dada uma imagem,a função retorna:
        *Uma lista; em que cada elemento da lista correspondente a um triângulo e
        cada elemento é composto por 5 elementos:
            (a) As coordenadas dos vértices do triângulo
            (b) O perímetro de cada lado do triângulo
            (c) A área do triângulo
            (d) Coordenadas do ponto que não está na hipotenusa
            (e) Coordenadas do ponto médio da hipotenusa
        *A imagem em tons de cinza da correspondente imagem de entrada
        *A imagem em P&B da correspondente imagem de entrada

    @parâmetro img Arquivo de imagem

    """
    #abs(t1-t2)/max(t1, t2) <= 0.1

    triangles, im_gray, im_bw = getTriang(img)



    tot_a = 0
    x = y = 0
    l1 = l2 = l3 = m = 0.0
    p1 = p2 = p3 = []
    med_tri = []
    box = []
    counter = 0

    for i in triangles:
        p1, p2, p3 = i
        #Encontrando o valor do lado 1
        x = (p1[0] - p3[0])**2
        y = (p1[1] - p3[1])**2
        l1 = math.sqrt(x + y)
        #Encontrando o valor do lado 2
        x = (p1[0] - p2[0])**2
        y = (p1[1] - p2[1])**2
        l2 = math.sqrt(x + y)
        #Encontrando o valor do lado 3
        x = (p2[0] - p3[0])**2
        y = (p2[1] - p3[1])**2
        l3 = math.sqrt(x + y)

        '''
        Se o módulo da diferença entre os dois menores lados for menor
        do que 7 quer dizer que esse triângulo pode ser um
        triângulo isósceles
        '''


        if ((abs(sorted([l1, l2, l3])[0] - sorted([l1, l2, l3])[1]) / sorted([l1, l2, l3])[1]) <= 0.12):

            box.append([p1, p2, p3])
            box.append(sorted([l1, l2, l3]))
            box.append((sorted([l1, l2, l3])[0]*sorted([l1, l2, l3])[1])/2)
            med_tri.append(box.copy())
            box.clear()

    #[[[37, 1101], [38, 1120], [57, 1120]], [19.0, 19.026297590440446, 27.586228448267445], 180.74982710918425]


    past = []
    #Ordena a lista em ordem descrescente em função da área
    #Debug: Tirei o reverse=True
    med_tri.sort(key=lambda x: x[2])
    for i in range(len(med_tri)):
        #Se não for o útimo elemento da lista
        if i < (len(med_tri) - 1):
            #Se a divisão da diferença absoluta entre o tringulo n e o
            #trianlgulo n+1 pelo maior triângulo entre eles não for <= a 1%,
            #esse não é um dos nossos
            if (abs(med_tri[i][2] - med_tri[i+1][2])/max(med_tri[i][2], med_tri[i+1][2])) <= 0.08:

                past.append(med_tri[i])
        #Se for o último elemento da lista
        else:
            #Se a divisão da diferença absoluta entre o último triângulo e o
            #triângulo anterior pelo maior triângulo entre eles não for <= a 1%,
            #esse não é um dos nossos
            if (abs(med_tri[i][2] - med_tri[i-1][2])/max(med_tri[i][2], med_tri[i-1][2])) <= 0.08:
                past.append(med_tri[i])
    past.sort(key=lambda x: x[2], reverse=True)
    med_tri.clear()
    med_tri = past.copy()[:4]


    for i in range(len(med_tri)):
        p1, p2, p3 = med_tri[i][0]
        #Encontrando o valor do lado 1
        x = (p1[0] - p3[0])**2
        y = (p1[1] - p3[1])**2
        l1 = math.sqrt(x + y)
        if l1 == med_tri[i][1][2]:
            #Ponto médio na hipotenusa
            p_m = [((p1[0] + p3[0])/2),((p1[1] + p3[1])/2)]
            med_tri[i].append(p2.copy())
            med_tri[i].append(p_m.copy())
        #Encontrando o valor do lado 2
        x = (p1[0] - p2[0])**2
        y = (p1[1] - p2[1])**2
        l2 = math.sqrt(x + y)
        if l2 == med_tri[i][1][2]:
            #Ponto médio na hipotenusa
            p_m = [((p1[0] + p2[0])/2),((p1[1] + p2[1])/2)]
            med_tri[i].append(p3.copy())
            med_tri[i].append(p_m.copy())
        #Encontrando o valor do lado 3
        x = (p2[0] - p3[0])**2
        y = (p2[1] - p3[1])**2
        l3 = math.sqrt(x + y)
        if l3 == med_tri[i][1][2]:
            #Ponto médio na hipotenusa
            p_m = [((p2[0] + p3[0])/2),((p2[1] + p3[1])/2)]
            med_tri[i].append(p1.copy())
            med_tri[i].append(p_m.copy())
    return med_tri, im_gray, im_bw



def getPTCrop(img):
    def getCenterOfMass(points):
        average_x, average_y = 0, 0
        for point in points:
            average_x += point[0]
            average_y += point[1]
        
        return [int(average_x/len(points)), int(average_y/len(points))]

    def getDistance(points_01, points_02):
        x1, y1 = points_01
        x2, y2 = points_02
        return math.sqrt((x1 - x2)**2 + (y1 - y2)**2)

    points_list = getInfoTriang(img)[0]

    triangles_centers = []
    for points in [p[0] for p in points_list]:
        triangles_centers.append(getCenterOfMass(points))
    page_center = getCenterOfMass(triangles_centers)

    quad = []
    comp = []

    for points in points_list:
        pt1, pt2, pt3 = points[0]
        distances = {
            getDistance(pt1, page_center): pt1,
            getDistance(pt2, page_center): pt2,
            getDistance(pt3, page_center): pt3
        }

        x = distances[min(distances.keys())][0]
        y = distances[min(distances.keys())][1]
        quad.append([[x,y], x+y])
        comp.append(x+y)
    maior = max(comp)
    menor = min(comp)

    for i in quad:
        if i[1] == menor:
            top = i[0]
        elif i[1] == maior:
            btt = i[0]
    return top, btt



def paper90(im_color):
    """
    Dada uma imagem,a função retorna:
        *A imagem orientada em 90°

    @parâmetro img Arquivo de imagem a ser orientado

    """

    def getCenterOfMass(trg):
        return (trg[0][0][0]+trg[0][1][0]+trg[0][2][0])/3, (trg[0][0][1]+trg[0][1][1]+trg[0][2][1]+trg[3][1])/3


    def getProportion(a, b):
        return (abs(a[0]-b[0])/max(a[0],b[0])), (abs(a[1]-b[1])/max(a[1],b[1]))



    # A imagem só muda de tamanho aqui, depois da primeira vez que ela foi ajustada
    height, width = im_color.shape[:2]
    if min(width, height) > 2700:
        im_color = resizeImg(im_color, 2700)
    elif min(width, height) < 1260:
        im_color = resizeImg(im_color, 1260)

    height, width = im_color.shape[:2]
    triangles, im_gray, im_bw = getInfoTriang(im_color)

    #Verificar de existem 4 triângulos de interesse
    ang_1 = []
    for i in triangles:
        #Coordenadas do ponto fora da hipotenusa
        x1, y1 = i[3]
        #Coordenadas do ponto médio na hipotenusa
        x2, y2 = i[4]

        #     y2 - y1
        #m = ---------
        #     x2 - x1
        m = ((y2-y1)/(x2-x1))

        #Função que tranforma a tg em graus (positivos e negativos)
        #Nesse caso, essa variável está recebendo a soma de todos
        #os ângulos, para depois tirar a média

        ang_1.append(TG2AN(m))
    #Média dos angulos dos 4 triângulos
    ang_1.sort(reverse=True)
    angulo = (sum(ang_1))/len(ang_1)

    #Aqui nós adicionamos bordas brancas a imagem sem alterar o tamanho do
    #arquivo original, isso soluciona o problema de cortar o QRCode
    porcem = 0.04
    canvas = np.ones((int(height + (height*porcem)),int(width + (width*porcem)),3),np.uint8)*255

    canvas[0+ int((height*porcem)/2):height + int((height*porcem)/2) , 0 + int((width*porcem)/2): width + int((width*porcem)/2)] = im_color[0:height , 0: width]
    im_color = canvas

    height, width = im_color.shape[:2]
    x_1 = 0
    x_2 = min(width, height)
    #Se a imagem estiver de ponta cabeça
    if y2 > y1:
        if angulo < 0:
            angulo -= 180
            x_1 = (max(height, width)) - (max(height, width)) - (min(height, width))
            x_2 = (max(height, width))
        else:
            angulo += 180
            #Se a imagem estiver virada =~ -45º (inclinada bem para a direita)
            if x1 < x2:
                angulo -= 180
    #Se a imagem estiver virada =~ 45º (inclinada bem para a esquerda)
    elif x1 > x2:
        angulo += 180
        x_1 = (max(height, width)) - (max(height, width)) - (min(height, width))
        x_2 = (max(height, width))

    _max = max(width, height)

    #Girando a a imagem #########################
    #Calculates an affine matrix of 2D rotation
    #getRotationMatrix2D(center, angle, scale)
    rotation_mat_r = cv2.getRotationMatrix2D(((_max/2), (_max/2)),angulo + (45), 1)
    #The warpAffine parameter borderMode can be used to control how the background will be handled. You can set this to cv2.BORDER_CONSTANT to make the background a solid color, then choose the color with the parameter borderValue. For example, if you want a green background, use
    rotated_img = cv2.warpAffine(im_color, rotation_mat_r, (_max, _max),borderMode=cv2.BORDER_CONSTANT, borderValue=(255,255,255))


    rotated_img = rotated_img[0:_max, x_1:x_2]
    #Salva imagens
    #storeImg(im_color, rotated_img)


    triangles = getInfoTriang(rotated_img)[0]

    #Segunda correção de ângulo

    sec = []

    for i in triangles:
        if len(sec) < 4:
            sec.append(list(getCenterOfMass(i)))
    sec.sort(key=lambda x: x[1])


    if len(sec) == 4:

        if (abs(getProportion(sec[2],sec[3])[1]) > 0.01) or abs(ang_1[0]-ang_1[3]/max(ang_1[0], ang_1[3])) > 0.035:
            x1, y1 = sec[2]
            x2, y2 = sec[3]
            x3, y3 = sec[0]
            x4, y4 = sec[1]
            m_1 = ((y2-y1)/(x2-x1))
            m_2 = ((y4-y3)/(x4-x3))
            angulo = (TG2AN(m_1)+TG2AN(m_2))/2
            height, width = rotated_img.shape[:2]
            _max = max(width, height)
            x_1 = (max(height, width)) - (max(height, width)) - (min(height, width))
            x_2 = (max(height, width))
            rotation_mat_r = cv2.getRotationMatrix2D(((_max/2), (_max/2)),angulo, 1)
            rotated_img = cv2.warpAffine(rotated_img, rotation_mat_r, (_max, _max),borderMode=cv2.BORDER_CONSTANT, borderValue=(255,255,255))
            rotated_img = rotated_img[0:height, 0:width]

    return rotated_img

def getOurSqr(im_color):
    def getCenterOfMass(square):
        return (square[0][0]+square[1][0]+square[2][0]+square[3][0])/4, (square[0][1]+square[1][1]+square[2][1]+square[3][1])/4

    def getSum(square):
        return (square[0][0]+square[1][0]+square[2][0]+square[3][0]), (square[0][1]+square[1][1]+square[2][1]+square[3][1])

    def getProportion(a, b):
        return (abs(getSum(a)[0]-getSum(b)[0])/max(getSum(a)[0],getSum(b)[0])), (abs(getSum(a)[1]-getSum(b)[1])/max(getSum(a)[1],getSum(b)[1]))

    top, btt = getPTCrop(im_color)

    im_crop = im_color[top[1]:btt[1], top[0]:btt[0]]


    height, width = im_crop.shape[:2]
    im_gray, im_bw = getImgNoColor(im_crop)

    contours, _ = cv2.findContours(cv2.threshold(im_bw, 0, 255, cv2.THRESH_BINARY_INV)[1], cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    sqr = []

    for cnt in contours:
        approx = cv2.approxPolyDP(cnt, 0.04 * cv2.arcLength(cnt, True), True)

        if len(approx) == 4:
            sqr.append(approx)


    squares = formatShape(sqr)

    question_squares = []
    alternative_squares = []

    threshold = 0.02

    squares.sort(key=lambda x: getCenterOfMass(x))
    # ponto na posicao 0 eh o ponto mais a esquerda superior
    cmx, cmy = getCenterOfMass(squares[0])
    if cmx / width < threshold and cmy / height < threshold:
        del squares[0]

    squares.sort(key=lambda x: getCenterOfMass(x), reverse=True)
    # ponto na posicao 0 eh o ponto mais a direita inferior
    cmx, cmy = getCenterOfMass(squares[0])
    if (width - cmx) / width < threshold and (height - cmy) / height < threshold:
        del squares[0]

    # ponto na posicao 0 eh o ponto mais a esquerda inferior
    cmx, cmy = min(np.array([getCenterOfMass(x) for x in squares])[0]), max(np.array([getCenterOfMass(x) for x in squares])[1])

    if cmx / width < threshold and (height - cmy) / height < threshold:
        del squares[0]

    cmx, cmy = max(np.array([getCenterOfMass(x) for x in squares])[0]), min(np.array([getCenterOfMass(x) for x in squares])[1])
    # ponto na posicao 0 eh o ponto mais a direita superior
    cmx, cmy = getCenterOfMass(squares[0])

    if (width - cmx) / width < threshold and cmy / height < threshold:
        del squares[0]


    threshold_2 = 0.06
    squares.sort(key=lambda x: getCenterOfMass(x)[0], reverse = True)
    for i in range(len(squares)-1):
        p_m1 = getCenterOfMass(squares[i])[0]
        p_m2 =  getCenterOfMass(squares[i+1])[0]
        if ((abs(p_m1 - p_m2)/max(p_m1, p_m2)) <= threshold_2):
            question_squares.append([squares[i], getCenterOfMass(squares[i])])
            if i == (len(squares) - 2):
                question_squares.append([squares[i+1], getCenterOfMass(squares[i+1])])


    squares.sort(key=lambda x: getCenterOfMass(x)[1], reverse = True)
    for i in range(len(squares)-1):
        p_m1 = getCenterOfMass(squares[i])[1]
        p_m2 =  getCenterOfMass(squares[i+1])[1]
        if ((abs(p_m1 - p_m2)/max(p_m1, p_m2)) <= threshold_2):
            alternative_squares.append([squares[i], getCenterOfMass(squares[i])])
            if i == len(squares)-2:
                alternative_squares.append([squares[i+1], getCenterOfMass(squares[i+1])])


    question_squares.sort(key=lambda x: x[1][0])
    for i in range(len(question_squares)-1, -1, -1):
        if (i == len(question_squares)-1):
            if not(getProportion(question_squares[i][0], question_squares[i-1][0])[0] <= threshold_2):
                del question_squares[i]
        elif (i == 0):
            if not(getProportion(question_squares[i][0], question_squares[i+1][0])[0] <= threshold_2):
                del question_squares[i]
            #Debug: Esse +0.02 tá feio pacas
        elif not((getProportion(question_squares[i][0], question_squares[i-1][0])[0] <= threshold_2) or (getProportion(question_squares[i][0], question_squares[i+1][0])[1] <= threshold_2)):
            del question_squares[i]


    alternative_squares.sort(key=lambda x: x[1][1])
    for i in range(len(alternative_squares)-1, -1, -1):
        if (i == len(alternative_squares)-1):
            if not(getProportion(alternative_squares[i][0], alternative_squares[i-1][0])[1] <= threshold_2):
                del alternative_squares[i]
        elif (i == 0):
            if not(getProportion(alternative_squares[i][0], alternative_squares[i+1][0])[1] <= threshold_2):
                del alternative_squares[i]
        elif not((getProportion(alternative_squares[i][0], alternative_squares[i-1][0])[1] <= threshold_2) or (getProportion(alternative_squares[i][0], alternative_squares[i+1][0])[1] <= threshold_2)):
            del alternative_squares[i]

    return question_squares, alternative_squares, im_crop



def getAnswers(img):
    def getProportion(a, b):
        return abs(a-b)/max(a,b)


    def formatCircle(coord):
        x = y = 0
        circles = []
        for i in range(len(coord)):
            x = coord[i][1][0]
            y = coord[i][1][1]
            #Círcles recebe as coordenadas do centro de massa
            circles.append([[x, y]])

        dist = []
        for i in range(len(coord)):
            for j in range(len(coord[i][0])):

                x = coord[i][0][j][0][0]
                y = coord[i][0][j][0][1]
                #Dist vai receber a ditância desse ponto até o centro de massa
                x1 = (x - circles[i][0][0])**2
                y1 = (y - circles[i][0][1])**2
                dist.append(math.sqrt(x1 + y1))
            raio = sum(dist)/len(dist)
            #Circles recebe a área do círculo
            circles[i].append(math.pi*(raio**2))
            dist.clear()
        circles.sort(key=lambda x: x[1])


        return circles

    n, alt, img_croped = getOurSqr(img)


    n.sort(key=lambda x: x[1][1])
    alt.sort(key=lambda x: x[1][0])
    x0 = max(n[0][0][0][0],n[0][0][1][0],n[0][0][2][0],n[0][0][3][0])
    y0 = max(alt[0][0][0][1],alt[0][0][1][1],alt[0][0][2][1],alt[0][0][3][1])
    x1 = max(alt[len(alt)-1][0][0][0],alt[len(alt)-1][0][1][0],alt[len(alt)-1][0][2][0],alt[len(alt)-1][0][3][0])
    y1 = max(n[len(n)-1][0][0][1],n[len(n)-1][0][1][1],n[len(n)-1][0][2][1],n[len(n)-1][0][3][1])

    im_gray, im_bw = getImgNoColor(img_croped)

    contours, _ = cv2.findContours(cv2.threshold(im_bw, 0, 255, cv2.THRESH_BINARY_INV)[1], cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    circ = []

    for cnt in contours:
        approx = cv2.approxPolyDP(cnt, 0.001 * cv2.arcLength(cnt, True), True)
        if len(approx) > 4:
            M = cv2.moments(approx)
            cX = int(M["m10"] / M["m00"])
            cY = int(M["m01"] / M["m00"])
            circ.append([approx, [cX, cY]])

    circles = formatCircle(circ)

    if len(circles) > 0:
        #Elimina os círculos que estão fora da área de busca
        for i in range(len(circles)-1, -1, -1):
            if (circles[i][0][0] <= x0 + int(x0*0.7)) or (circles[i][0][0] >= x1) or (circles[i][0][1] <= y0) or (circles[i][0][1] >= y1 + int(y1*0.07)):
                del circles[i]

        #Elmina os círculos que contem um área diferente dos nssos (a maioria com o maior tamanho)
        for i in range(len(circles)-1, -1, -1):
            if i == len(circles)-1:
                if getProportion(circles[i][1], circles[i-1][1]) > 0.2:
                    del circles[i]
            else:
                if getProportion(circles[i][1], circles[i+1][1]) > 0.2:
                    del circles[i]

        #Vamos ver quais circulos correspondem a quais questões
        threshold = 0.055
        answ = []
        box = []

        c = 0
        for i in range(len(n)):
            for j in range(len(circles)):
                if getProportion(n[i][1][1], circles[j][0][1]) < threshold:
                    #A resposta na posição i vai receber x e y do centro desses circulo
                    c += 1
                    answ.append(n[i].copy())
                    box.append(circles[j][0].copy())
            # Para o caso de questões não marcadas
            if c == 0:
                answ.append(n[i].copy())
                box.append([0,0])
            c = 0

        for i in range(len(answ)):
            answ[i].append(box[i].copy())

        letters = []
        for i in range(8):
            letters.append(i+1)

        counter = 1
        gabarito_aluno = []
        for i in range(len(answ)):
            #Verifica se a resposta corresponde a alguma questão
            if answ[i][2][0] != 0:
                for j in range(len(alt)):
                    #Verifica se resposta i corresponde à alternativa j
                    if getProportion(alt[j][1][0], answ[i][2][0]) < threshold:
                        #A primeira ocorrencia anda não pode ter sido repetida
                        #Porque ainda não tem outra alternativa "repetida" para
                        #ser comparada
                        if i > 0:
                            if answ[i][1][1] != answ[i-1][1][1]:
                                # Evita de aumentar os índices em mais de uma unidade por vez
                                if counter == gabarito_aluno[i-1][0]:
                                    counter += 1
                        gabarito_aluno.append((counter, letters[j]))
            #Senão, gabarito na posição i recebe apenas o contador (indicando
            # a qual questão corresponde essa resposta
            else:
                # Se não for a primeira ocorrência, conta antes de adicionar
                if i != 0:
                    # Evita de aumentar os índices em mais de uma unidade por vez
                    if counter == gabarito_aluno[i-1][0]:
                        counter += 1
                    gabarito_aluno.append((counter, 0))
                # Se for a primeira ocorrência, conta depois de adicionar
                else:
                    gabarito_aluno.append((counter, 0))
                    # Evita de aumentar os índices em mais de uma unidade por vez
                    if counter == gabarito_aluno[i-1][0]:
                        counter += 1

    else:
        gabarito_aluno = []
    return gabarito_aluno


def getGrades(gabarito, gabarito_aluno):
    keys = []

    for i in gabarito_aluno:
        if i[1] != 0:
            keys.append(i[0])

    nota = []

    for i in range(len(gabarito_aluno)):
        for j in range(len(gabarito)):
            #Verifica se essa questão da prova do aluno equivale a questão da
            #prova gabarito
            if gabarito_aluno[i][0] == gabarito[j][0]:
                ##verifica se a questão está correta
                if gabarito_aluno[i][1] == gabarito[j][1]:
                    #Verifica se essa questão "correta" só aparece uma vez
                    if keys.count(gabarito[j][0]) == 1:
                        nota.append([gabarito_aluno[i][0], gabarito[j][2]])
                    #Verifica se essa questão "correta" só aparece mais uma vez
                    elif keys.count(gabarito[j][0]) > 1:
                        nota.append([gabarito_aluno[i][0], 0])
                    else:
                        nota.append([gabarito_aluno[i][0], -1])
                else:
                    nota.append([gabarito_aluno[i][0], 0])

    notas = []
    box = []
    for i in range(len(nota)):
        if nota[i] not in box:
            box.append(nota[i])


    for i in range(len(box)):
        if keys.count(box[i][0]) != 1:
            box[i][1] = -1
        notas.append(box[i][1])

    answ_clear = []

    for i in range(len(notas)):
        if notas[i] != -1:
            for j in range(len(gabarito_aluno)):
                if gabarito_aluno[j][0] == i+1:
                    answ_clear.append(gabarito_aluno[j][1])
        else:
            answ_clear.append(-1)

    warning = []

    for i in range(len(notas)):
        if notas[i] == -1:
            warning.append(f'Nenhuma ou mais de uma opção foi marcada na questão {i+1}')

    return notas, answ_clear, warning
