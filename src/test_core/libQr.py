import cv2
import pyzbar.pyzbar as pyzbar
import pyqrcode
import os

def leQr(img) -> dict:
    """
    Dada uma imagem, a função retorna:
        *A informação contida no QRcode

    @parâmetro img Arquivo de imagem no qual está inserido o QRcode

    """

    im_gray = cv2.split(cv2.cvtColor(img, cv2.COLOR_BGR2HSV))[2]
    _, im_bw = cv2.threshold(im_gray, 0, 255, cv2.THRESH_OTSU | cv2.THRESH_BINARY)

    final = {}

    try:
        qr_info = pyzbar.decode(im_bw)

        for obj in qr_info:
            text = obj.data

        # Texto com formatação
        new_text = text.decode('utf-8')
        if len(new_text) != 40:
            new_text = None
        else:
            new_text = new_text.replace('#', '') # Substitui os "#" por null
            id_prova = new_text.split('.')[0]
            id_aluno = new_text.split('.')[1]

            # Esse if verifica se o primeiro número e o segundo número podem
            # ser convetidos para numeral
            if ((id_prova.isdigit()) and (id_aluno.isdigit())):
                final = {
                    'id_prova': int(id_prova),
                    'id_aluno': int(id_aluno)
                }
    except Exception as error:
        print(f'Something went wrong!\n{error}')
        
    #retorna apenas o texto contido
    return final

def escreveQr(texto):
    """
    Codifica um texto em QRcode e retorna-o como imagem:

    @parâmetro texto Texto que vai ser codificado em QRcode

    """
    code = pyqrcode.create(texto)

    CURRENT_FOLDER = os.path.dirname(__file__)
    logo_qr = cv2.cvtColor(cv2.imread(f'{CURRENT_FOLDER}/../assets/logo_qr.jpg'), cv2.COLOR_BGR2RGB)

    logo_h, logo_w, _ = logo_qr.shape
    
    code.png('qr.png', scale=6)
    img = cv2.imread('qr.png')

    img_h, img_w, _ = img.shape
    # Mudando o tamanho da logo pra 20% do tamanho do qr code
    prop = logo_w/logo_h
    logo_qr = cv2.resize(logo_qr, (int(img_w*20/100), int((img_w*20/100)  *prop)))
    
    logo_h, logo_w, _ = logo_qr.shape

    img[int(img_h/2-logo_h/2):int(img_h/2+logo_h/2), int(img_w/2-logo_w/2):int(img_w/2+logo_w/2)] = logo_qr

    return img



def formataQr(msg):
    """
    Dada uma string, prenche essa string até atingir o tamanho 20 e retorna essa
    nova string.

    @parâmetro msg String que será prenchida

    """
    txt_msg = str(msg)
    zeros = ''
    if len(txt_msg) < 9999999999999999999999999999999999999999:
        for i in range(40-len(txt_msg)):
            zeros += '#'
        txt_msg = zeros + txt_msg
    return txt_msg