<?php
namespace tests\Business;

use TestCase12;
use App\Entities\CabecalhoEmail;
use App\Repository\CabecalhoEmailRepository;
use App\Business\CabecalhoEmailBO;
use App\Entities\CabecalhoEmailUf;
use App\Entities\Uf;
use App\To\CabecalhoEmailFiltroTO;
use App\Repository\UfRepository;
use App\Repository\CabecalhoEmailUfRepository;
use App\Business\HistoricoBO;
use App\Repository\HistoricoRepository;
use App\Entities\Historico;
use App\Exceptions\NegocioException;
use App\Exceptions\Message;
include 'TestCase12.php';

/**
 * Teste de Unidade referente á classe CabecalhoEmailBO.
 *
 * @author Squadra Tecnologia S/A.
 */
class CabecalhoEmailTest extends TestCase12
{

    const ID_CABECALHO_EMAIL = 1;

    const TITULO_CABECALHO_EMAIL = 'Título de Cabecalho E-mail 1';

    const ID_UFS = [
        1,
        2,
        3
    ];

    const DESCRICAO_UFS = [
        'AC',
        'RO',
        'SC'
    ];

    const ID_RESPONSAVEL = 5;

    const NOME_IMG_CABECALHO = 'cabecalho.jpg';
    
    const NOME_IMG_RODAPE = 'rodape.png';
    
    const IMG_CABECALHO_RODAPE_BASE64 = "/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxIQDxUSEBERExUVFhcVEBISEBEQEBUQFREXFhUWFRYYHiggGholGxUVITEhJSkrLzAuFx8zRDMtNygtLisBCgoKDg0OGxAQGjcmICUtLS0tMi8vLS0wLy8wLS0tLS0vLy0tLS0tLS8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAMkA+wMBEQACEQEDEQH/xAAbAAEAAgMBAQAAAAAAAAAAAAAABgcBBAUDAv/EAEkQAAIBAgEHBgYQBgEFAQAAAAECAAMRBAUGEiExQVEHEyJhcYEUMpGSs9EWIzVCUlNUYnJzgpOhscHSJTM0Y7LCJEOUorTDF//EABsBAQABBQEAAAAAAAAAAAAAAAAGAQMEBQcC/8QAPBEAAgECAwQGBwYGAwEAAAAAAAECAwQFERIhMUFRBhNhcZGxFBUiUoGhwTIzNFNy0SMkNWLh8BZCQ6L/2gAMAwEAAhEDEQA/AJLOaEuEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQBAEAQVEFBAEFRBQQVEAQBAEFBBUQUEAQBBUQUEAQVEFBBUQUEFRBQQBAEAQBAEAQBBUgGX88MTQxtShT5rQVkA0qZLWZEY69L5xkwscFtq1pGtPPNpvf3mgucRq067px3ZlgGRBrJm+W4xKAgOdOd+Jw2MqUafNaK6FtKmS3SpqxudLiTJhhmC21xbRqTzzfaaG9xGtSrOEdxo+zPKPxSf9tV9cyfUeHe9/wDSLHrK75fI38gZ046tiqVOrTUIzEORQqKQNEnaTYawJi32E2NG3nOnLals2oyLa/uZ1VGS2dxq5Yz2xVLE1aaczopUZVvTYnRViBc6UvWuCWVSjGc282tu0tV8SrwqOKWzPkafs/xn9j7pv3TJ/wCOWW/b4lr1tcdngYflBxgB/kfdN+6ef+P2PN+JX1pc8vkbuceVsZSx5C1KyUS9MJ0SKZvTQsFYix1lthlqxs7Odpm4pySl38cj3c3FeNfe0m0SzOvOJcDTB0dOo5IppewsNrMeAuO2/aRH8MwuV5Uebyit7+iNreXit4ri3uIIc8coVCWRtQ2inQVlHbcE+UyVLBcPprKS8WaT1jdS2x8jtZqZ5169ZaNWkKmlsekNFlG9nBNio4i3fsmsxTA7ejSdWnLTlz3PuM2yxKrUnomsyeSIm8EA5OcOcFLBIDUuzN/Lpr4zdfUvX+c2WH4ZVvJZR2Jb2Yl1dwt1t38iBYrPzGVGtT5unfxVROce3a179wEllPo/ZUo/xM33vJGjnitxN+x8jOEz9xdNrVRTqgeMrJzb9xW1u8GUq9H7OpH+Hmu1PNCGK3EH7W0n+Qcu0cZT0qRIItzlNtToTsvxHAj87iRLEMOq2c9M93Bm9tbuncRzjvPTLWV6WEpc5WJ4Ko1u7cFH67BPFlYVbueimu98j1cXMKEdUiv8fn/ina1FadIe9AXnKneW1eQSXUejtrTX8VuT8DQ1MWrzeUNh44TPvGIRpmnUG2z0wtx1FLeXXLlTALKosobO5nmOKXMH7XzRO8285KWNU6N0qKLvSY3IF7XU++W+/wAoFxIriOFVbN5vbF7n+5u7S9hcbt/I7U1RmiAIAgCAIAgCAIKlQZ3e6lb6dP0VOdGwr+nQ/S/qRK+/FvvRcB2znUt7JYtxiURUqHlA90K3YnoVnRcF22EO5/UieIfi38C0Hy9hbn/lYfb8fT9chM8PunJtU5eDJFG5oaV7SPqjlfD1GCJiKLsdirVRmOq+oA69QMtTsrmEXKcGl2o9xuKMnlGSzN28x9UlszLulcioM6D/ABWr9bT/AMEnRMOeeHRf9r+pFLzZdvvRcDMb7TOeynLN7SVqMctxyMp5EGJr03q1CUpNppRChQanF2uS2zYAPzvn22Iej0ZQpx9qWxvs7DErWnWzUpvYtyITyoUWGKpub6DUtFTu0lqOWHkdZJejNSLoSjxzzZp8Zg+tT4ZEhzKy9hjhqVAOtKoihWRrJpPvZTsbSNzx1zU4zh90q8q2TlF7suH7Gfh91QdNQ3MklHBU0d3Wmiu9ucYKAzW2aR3zS1LirOKhOTaXA2MaUItyit57THLhhmAFzqA1k9U9RjqaSKSeSzKepiplTKG0jnWvf4ugouAB1L5SeudHbhhtlsW5eLIklK8uMnxLXyXkylhk0KCBBvI8ZjxZtrGQG6va1zPVUl+xKKNtTpLKKMZVyXSxSFKyBhuPv1PFG2gytre1raalTl+zFe2p1o5SRr5vZCpYKloU7sx11KhA0nP6Abh+pJl3EMRq3s9Ut3BHi1tIW8clvK2zmxj43KDKuwPzNEHYAraJbvN2vwtwk2w23jaWafFrUyOXdSVxcOK55ItTI+Q8HgcPooivUt7ZVanpM7EX6wFsRq2a5rLm4nVepvZwNhQoaHp5ELzm0apN0W3Xa/4euWKNV05Zxk8zZzoKpDKUdhDqNd8HiFqUzrQhl1+Mu9T1EXBkhShe27jJb9nx5karQlZ3Gz/UXXQqh0V12MAy9jC4/AzmtWm6c3F8HkSyEtUVLmfctnoQBAEAQBAEAQVKgzu91K306foqc6NhX9Oh+l/UiV9+Lfei4DtnOpb2SxbjEoipUWf5/iNbsp+hWdFwR5WMH2MiWJLO5a7iSHk2S/8AUv8AdL+6aeXSiSbXV/M2CwWLWeo3MjZjJhsRTrCuzFCSFNNVBujLtv8AOmNd9IJXNCVJwyz7S9QwpUqimpbiWyNm2ZUOdHurV+tp/wCCTouG/wBNj+l/Uid7+Mfei322znct7JWtxiUKmplTJtLE0zTrLpKdY3MrDYyncdcyrW7q209dN7SzXoQrR0zWwgOVuT2qlzhqi1R8CpZKlupvFY+bJbadJKU8o1lp7Vu/c0VfB6kdtN5mjm3nHWwVYUaxfmg2hUpve9LXa638W222wi/bMrEcMoXlF1KaWrLNNcSxaXlShU0S3cUy2DOftZPIlSPHF0y9N1G1kZR2lSP1ly3ko1Yt8GjxVjqg0uRVXJ9iVpY9A+rTVqYvqs5sQD13W3aZPsdpOrZNw25ZP4EYw2ap3GUu4tqc8JUVvlWtlZKlZlOIFJXqFSAmiKQdiD2aNpOLWnhU6cFLLU0ue8jdeV6pSazyz+Rt8n2WsRXxLrWrNUUU9IBrWDaai+ocCZjY9Y29ChGVOGTzL2F3FWrUam89hFF9ox50x4lZwbj5zLfX2gzfS/jWS08YowKLVK7Wrg2TJcrNYqWI17m0hsFtouN0j0k2ufyJPCKTfA5OLrFja4PcfXPMVEvPVmR/Lr9MDV0V124m5t5CPLJBhkcqTfNkYxeeqso8Ui4MjUDTw1FG2pSpq3aqAGc/vqiqXE5Lc5MkNvHTSinwRuTFLwgCAIAgCAIAgqins8mtlKueDofJRpzpGELPD6a7H5siOIPK5kzvf/pL/JU+/b9k1T6L028+sfh/kzVjUl/0N3I2fbYjEU6Jw6qKjBSwqliL77aOuY130dhQoSqqo3kuX+S9QxV1aihp3kXz/F8o1uyn6FJu8EWdjD4mtxJ5XLJM3KTTv/TP96v7ZppdF5tt9YvmbFY1FLLSzbyRnymIrpRFB1LkjSNQECyk7LdUxrro9O3oyq608i7QxaNWooKO8lsjhtioc6PdWr9bT/wSdFw3+mx/S/qRO8/FvvRb7bZzuW9ksW4xKAh2fOctfCulOihS9mNZkDI9j4i7t2vf2bZJsEwuhcwc6rz4ZLeu00+I3tWjJRgviaeH5Rxoe2YY6XzKvRJ7xcfjMmp0X9r2KmztW35FmONez7UdpFRTqZTxxstmrNd9G5VKYAUsTwCga957ZvZSpYfaZN7IrZ2s1kVO6r5ri/Auec0k83mTCKyWQlCpW2fObD06rYmgpKMdKoE8am+0sLa9EnXfcb7rSb4Li0KtNW9Z7VsWe5ojmIWMoT6ynu8jOSeUKoiBcRTFW2oVFbQcj5wsQT1i0XfRunUlqpS058OH+ClDF5wWmos/M8su5+PWptTo0hSVgVd2bTfRIsQNQC6t+vunuy6PU6E1UqSza3cilxis6sXGKyR0+TjItWkXxFRSiumhTVgQ7DSDaVtw1auN+Fr4XSK/pVEqMHm083yMnCbWcG6ktgz9zXaoxxOHUsbDnqai7GwsHUbzawI6h1xgWLQhFW9Z5cn9Bidi5PrafxIZhsqsup7sBquD0rcJvq1hGftU3kYtvik6a01Fmvmez5ZsOguvcz7R3DbLUMLTknN+BeqYy9OVOPxZ2czM2nxFVcRXUikp0xpXvVcG4sPgX1k79mvXbExjFIW9J0aX2msu5Fqws51p9bU3b+8s6QQkogCAIAgCAIAgCAc7FZBwtVy9TD02ZvGZluSQLC/cBM6liV1SgoQm0kY87SjOWqUU2eXsYwXyWj5s9+tr38xnn0G39xHph838LTcPTw9NWU3VlXWDxE8zxK6qRcJTbTPUbShB6oxSZ9YvIOGqualXD03Y20mZbk2Fh+AEpSxC6pQUITaSE7WhOWcops8fYzgvktHzZ79bXv5jPPoNv7iPTDZAwtNw9PD0lZdasFsQbW1eWeamJ3VSDhObaZ6haUYS1RjkzpTAMg59fIOGqVDUfD02ckEuV6RItY37hNhDELuEFCM2lyMadtQlLU0szoTXt5mSIB54iglRSlRVdTtVlDKe0GXKdadKWqDyfYeZwjNZSWZxauZeBY3OHt9GrXQeRWAm0hjd/FZKfyzMJ4dat5uJ1Mn5MpYddGhSWmDt0V1k/OO098wbi5r3DzqybMqlSpUllBZG3aY2ll3NC0aWM0LSqTRTNHIx2bGDrktUw6EnWWTSpMT1lCL982FDFbyksoTfmYtSxt57ZR+h9YDNrCUCGpYdAw2M2lUYHiC5JHdFbE7ysspTeXZsFOzt6bzjFHVt2zX6WZeaFo0sZo5mUc3sLiCWrUEZjtYAo57WUgnyzPoYleUFlTm8vFGLVtKFT7UUeODzVwdE3TDpcawXL1SDxGmTae62L3lVZTm/hs8jzTsLeDzjE7NprWpMy047jEo1kVEoBAEAQBAEAQBAEAQCOcoDlcn1CpIOlT1gkH+au8TdYBFSvYprg/I1+KSat20VvkXFVDiqA5x7GtSBBdiLc6txtk2u6NNUJ+ytz4LkRy3qT62Ob4l1zl5MxAOPnblbwXCO4NnboUuPOMDr7gC32ZtMIs/SbmMXuW19xh39x1NFtb3uKfXEuNlRwRsOm17jvnRHQptZaV4ETVWeeaZc2bmVBi8LTq6tIi1QDdVXU3dfWOoic2xO0drcyp8N67mS6zr9dRUvE6UwDKEFSmc58Qwx2IAdh7a+rSI99OmYdSg7Wm3FblwIddzl10snxOZ4U/xjee3rmb1NP3V4Ixdc+bHhT/GP57euOqp+6vBFdc+Znwh/hv57euOpp+6vBFOslzJdyeZLetWNeoXNOkegCxIattG/Yo19pXrkdx+7hQpdTBLVLsWxf5NvhdGVSeuT2LzOlypVCtOhZivSqbCR71eEw+jEIynUzWexGRjLajHIr7wl/jH89vXJd1NP3V4Gg1z5seFP8Y/nn1x1VP3V4Ia582PCX+G/nt646mn7q8EOsnzZs5OpVq9VKVNnLObDptYcWOvYBcnsli4dGhTlUmlklyRdoqpVmoJvaXPk/BLRorRS5Cro3Otid7HrJue+c3q3DrV+sfFkvhSVOloXIo8Vqg1F3uNR6TbR3zpsadNpPSvAhsqk03tLgzNUjJ9C5JJUsSTc9J2b9ZzvGWvTZ5cyWYen6PHM7M1hmCAIAgCAIAgCAIAgEb5Q/c6p9Kn6VZu+j342Pc/I12K/hmVnkT+rw/19L0qyc3n3FT9L8iNW/wB7HvXmXhOVk3EAqvlCytz+K5pT0KN16jVNtM91gv2Txk/wCy6i31vfLb8OBFcUuOtq6VuiRs4ZxTFXROgXKBt2mFDEeQ/nwm5VWLqdXntyz+Br3Tlp1ZbCV8m2VeaxDUGPRra04Cso/wBluPsrND0isuto9ct8fI2mE3Gip1b3PzLNkFJKIB5NhaZNzTpknaSikk+SXVcVUslJ+J4dKD3pGPA6fxdPzF9Ur6RV99+I6qn7q8CO575Rp4TDaKJTFWrdadkS6rbpvs3AgDrIm7wS3q3NfVKT0x2va/gjXYlVp0aeSSzZWWT8G9eqlKmLs5CrwHEnqAuT1CTavXjRpyqT3LaRulSlUmoLey6slZPTDUUo0/FQWvvZtrMesm575zG8up3NaVWXH/UTOhRVKCgjYq0VfxlVrbNJQ1uy8swqTh9l5HuUIy3o+PA6XxdP7tfVPXpFX334lOqp+6vA+K9KjTRndKSqoLMxRbBQLk7JdpVK9SahGTbfaeZxpQi5NLJFNZcyl4TiHq6IVSbU0AA0aY8Uat+89ZM6RZW/o9FQbzfF82Q+5rdbUcktnAnXJxkTm6RxNQdOoLUr7RR4/aIv2AcZFekWIa5+jwexb+//AAbzCrXTHrZcdxNRIubllE5RTRr1V+DUqDyOROr28s6UH2LyIPWWVSS7WXHm0mjgcOP7NO/aaYJnNsTlqu6j/uZMLRZUI9x0pgmQIAgCAIAgCAIAgCARvlD9zqn0qfpVm76PfjY9z8jXYr+GZWeRP6vD/X0vSrJzefcVP0vyI1b/AHse9eZeE5WTc5uceVBhcK9X3wFqYO+o2pfWeoGZ+GWjubiMOG99xi3lfqaTl4FLorOwAuzOwA3szsbDvJP4zpbcacM9yX0IelKcu1ls4nNpTk3wRbFlXSVtl8QOlpdQLEjsMgNLFZLEPSHuby+BKJWS9F6rjl8ypqbtTcMt1ZGBU71dTcauIIk9lGNSLT2przIsm4Sz4ou3IuURicPTrLYaa9IDXouNTL3MCJzG/tnbV5U3wezuJnbVlWpKaN2YhfEA+a1VUUs5CqoLMx2BQLknununTlUkoRW1nmclGLbKXziyu2MxDVTcL4tJT72kPFHadZPWTOmYfZxtKCprfx7WQ67uHXquXAmnJvkTQpnFOOlUFqN91K+tvtEeQDjIz0ixDXP0eL2Lf38vgbnCbXTHrZb3uJtIsboQBAIFyk5csBhKZ22euQd21E/Jj2Lxkv6OYfvuZru/c0WL3X/jH4kXzVyKcZiVpkHm16VYi46A97fix1eU7pvMUvlaUHP/ALPYu81dlbOvVUeHEuRVAFgAANQAFgANgE5rKTk22TBJJZIzKHopPOdNDGYgf3ah8rk/rOoYdPVa03/aiFXccq8l2ly4Cno0aa8EQeRAJza5edab7WTCisqcV2HvLBcEAQBAEAQBAEAQVEFCN8ofudU+lT9Ks3fR78bHufka7FfwzKzyJ/V4f6+l6VZObz7ip+l+RGrf72PevMvCcrJuVlyk5W53ECgp6NHW/XWYf6rYfaaTro7Z9VQdV75bu5fuRnF7jXU6tbl5mlmKKC4nnsRVp0xTF6Yd1XSqNqBFzrsLntKzKxt15W/V0YtuWx5cEWcNVNVddR5ZeZY3skwfyvD/AHqeuQv1XeflMkXptD30VnnklHwtqmHqU6iVemebZWC1CemDbiel9o8JOcHlW9HUa0WnHZt5cCM4gqfWuVN5pna5M8raFR8Mx1P06Vz/ANRR0wO1QD9gzV9JLPXTVeO9bH3Gbg9xlJ0nx3FjSFEiEAgnKTluwGEpnWbNXsdi7VTv2nqA4yW9HMPzzuZrsj9WaLFrvJdVH4/sRTNfIxxmJWnr0B0qzC+qmDsvxJ1DtJ3SQYneq0oOfF7F3/4NXZWzr1VHhxLlRQAAAAALAAWAA1ADqnNJycpOT3smCSSyRmeSogGhl3Ki4TDvWbXoiyL8KodSr5fIATumbYWcrqsqa+PcWLquqFNzZS2IrvVqM7ks7sWY6ySxO4fkO6dMp040qahFZJL5ENlKVSeb3st3NDIngeGCsPbH6dY8GtqXsUau253znmMX7u67cfsrYv3JXYW3UUlnve87c1JnCCpTuedL+JV14uv/AJ0kP+06RhM/5CEux+bIhfR/mpLtLitbVwnOpvOTZLYrKKQngqIAgCAIAgCAIKnKziy/SwVMNU6TNqp01I0mO89Sjef1mxw7Dat7PKOxLezEu7yFvHOW/kbGTMrUcSgejUVhvFwHU8GXaDLVzY17eemcWe6VzSqx1JkR5Rcu0mpeDU2V2LKapU3VFU3AJGrSJA1bgOsSQ9HsOqxqdfUWS4dpqsVu4OHVxefMieaeFNXHUFA2VA56lp9Mk+b+MkGKVVStJyfLLxNTY03OvFFtZayiuFw9Ss3vF6I+E51KvexAnPrG2dzXjSXHf3cSWXNbqabmykqtRnYsxLMxLMd5ZjcnvJnToxjTiktiRC5NzlnxZ1/Ynjvkz+dT/dMD1vZL/wBEZXq+490exPHfJn86n+6PW9l+Yh6vufdMHNTGgX8GfV86mf8AaFi1m3kqiDsLhbXE5eExLUqi1KZsyMGU9YNxfqmdVpRq03CW5oxoTcJKS3ou/JuNXEUUqp4rqGA3jiD1g3HdOXXVvKhVlTlwZNKNVVIKa4nnlrKaYWg9Z9YUdFd7OdSr3n8LndLljaSuqypR+PcebmuqFNzZSmLxLVajVKhuzksx6zw6uAnTaVKNKChFbEQ2c5Tk5S4lj5m18HhMMA2Kw3Ov0q3t1PUbak2+9GrtLHfIZjNO8u6/s03pjsWz5kiw929Cntks3vO97IsH8rw/31P1zT+rLz8p+BsPTLf30fVPL+EZgq4qgWYgKorIWLE2AAvrJMpLDruKcnTaS7Arui3kpI6Mwsi+VTn7lzwnEc0h9rokqODVdjt128Udh4zoGBWHo1DrJL2pfJciLYndddU0x3LzPPMejQ8I57E1qVNaWumtR0UtV3GxOxdvbbgZcxqpXVHq6EW3LflyPOGwpdZrqPJLmWP7IsH8qw/31P1yE+rLz8p+BI/TLf30PZFg/leH++p+uPVl5+U/Ar6ZQ99G9hcSlVA9J1dTezIwZTY2NiOsETFqUZ0paaiyfIuwnGa1ReaI1lHJmTa2INepiKfOEqWAxNNV0kCqLj7Im9t7vEaNFUYU/Zy5Gtq0LSpU6yUtveSinVDgMpDBhdWUgqQdhBG0TQzjKMnGSyZs4tNZx3H1PBUQBAEAQBAEAQCA585rVqlU4mjpVbgadO93TRFvaxvXfo7bk7b6phgeLUYU1QqezyfB9/aaHErGpKTqx2/7wIA6WJDDWNRBFiDwIOyStNS2pmiacd+w9sHhXrNoUkZ2+Cgue/gOszxVrU6UdVR5I9wpSqPKKzLRzMzY8DU1KtjWcWNtYRNR0Ad5uASeocLmC4zi3pctEPsr59pJcPseoWqX2vIj/KZlbSqJhlOpOnV+sI6I7lJP2xNr0btFCDrve9i7jCxes5SVNcNvxObmDkrwjFh2F0o2qNwNS/tY8oLfYmfjl6qFs4xe2Wz4cTFwy26ysm1sRbE56SoRmVMyqZRlP57ZK8GxjWFkqe2U+A0j0lHY1+4idFwe9VxarU/aWx/73ESxC3dKs8lsZIOTHK3j4VjxqUf/AKKPwa30pqOklqnpuI9z+hsMIrtZ0pd6OVn/AJc8IxHNIb06JI1bGrbHbu8Ufa4zPwKyjb0esl9qXyXIxMTuXWqaI7kRa83upGs0sXjUhpYvGpcxpfI3chH/AJeH+vo+mWY17JejVNv/AFfkX7aL66Oziizc+cueC4fRQ2q1brT4qvv37gbDrI4GQjA7FXFbVP7Mdr7+CJJiVy6VPKO9lSToGaIpkzN41LmNLF41LmNL5C8alzGl8i2+Tz3OpfSq/wDsPOf48/56WXJeRLMMX8skVVjv51T6b/5mTuhJdVDbwRF6sHreziy481v6DDfU0/8AATnOK/jKv6mS+z+4h3HUmvMgQBAEAQBAEAQBAPHEYSnU/mU6b/TRX/MS9C4qw2Rk18TxKlCW9I+6NJUFkVVHBVCjyCeJ1Zz2yefeVjCMfsrI+54PRm8rqfMZLkCYcm94SS3GJQCAIBkGVUmtzGSe8Eyrk3xGSF41y5jSuQvGuXMaY8heNcuY0x5C8a5cxpjyF41S5jSuQBlNTW4NJ7xeV1y5jTHkLxrlzGmPIXjXLmNMeQvGuXMaVyEo23vGWW4XldcuZTTHkYnlvMrkIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAIAgCAf//Z";

    /**
     * Teste de busca de Cabeçalho de E-mail por id.
     */
    public function testeGetPorIdComSucesso()
    {
        $cabecalhoEmail = $this->criarCabecalhoEmail();
        $cabecalhoEmailRespositoryMock = $this->createMock(CabecalhoEmailRepository::class);
        $cabecalhoEmailRespositoryMock->method('getPorId')->willReturn($cabecalhoEmail);

        $cabecalhoEmailBO = new CabecalhoEmailBO();
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailRepository', $cabecalhoEmailRespositoryMock);

        $this->assertEquals($cabecalhoEmailBO->getPorId(self::ID_CABECALHO_EMAIL), $cabecalhoEmail);
    }

    /**
     * Teste de busca de Cabeçalho de E-mail por id em cenário de id inexistente.
     */
    public function testeGetPorIdNaoEncontrado()
    {
        $cabecalhoEmail = null;
        $cabecalhoEmailRespositoryMock = $this->createMock(CabecalhoEmailRepository::class);
        $cabecalhoEmailRespositoryMock->method('getPorId')->willReturn($cabecalhoEmail);

        $cabecalhoEmailBO = new CabecalhoEmailBO();
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailRepository', $cabecalhoEmailRespositoryMock);

        $this->assertEquals($cabecalhoEmailBO->getPorId(self::ID_CABECALHO_EMAIL), $cabecalhoEmail);
    }

    /**
     * Teste de busca de Cabeçalho de E-mail com filtro.
     */
    public function testeGetPorFiltroComSucesso()
    {
        $cabecalhosEmail = $this->criarListaCabecalhosEmail();
        $cabecalhoEmailRespositoryMock = $this->createMock(CabecalhoEmailRepository::class);
        $cabecalhoEmailRespositoryMock->method('getCabecalhoEmailPorFiltro')->willReturn($cabecalhosEmail);

        $cabecalhoEmailBO = new CabecalhoEmailBO();
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailRepository', $cabecalhoEmailRespositoryMock);

        $cabecalhoEmailFiltroTO = new CabecalhoEmailFiltroTO();

        $this->assertEquals($cabecalhoEmailBO->getPorFiltro($cabecalhoEmailFiltroTO), $cabecalhosEmail);
    }

    /**
     * Teste de busca de Cabeçalho de E-mail com filtro nenhum registro encontrado.
     */
    public function testeGetPorFiltroNaoEncontrado()
    {
        $cabecalhosEmail = [];
        $cabecalhoEmailRespositoryMock = $this->createMock(CabecalhoEmailRepository::class);
        $cabecalhoEmailRespositoryMock->method('getCabecalhoEmailPorFiltro')->willReturn($cabecalhosEmail);

        $cabecalhoEmailBO = new CabecalhoEmailBO();
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailRepository', $cabecalhoEmailRespositoryMock);

        $cabecalhoEmailFiltroTO = new CabecalhoEmailFiltroTO();

        $this->assertEquals($cabecalhoEmailBO->getPorFiltro($cabecalhoEmailFiltroTO), $cabecalhosEmail);
    }

    /**
     * Teste de salvar Cabeçalho de E-mail.
     */
    public function testeSalvarComSucesso()
    {
        $cabecalhoEmail = $this->criarCabecalhoEmail();
        $cabecalhoEmailRespositoryMock = $this->createMock(CabecalhoEmailRepository::class);
        $cabecalhoEmailRespositoryMock->method('persist')->willReturn($cabecalhoEmail);

        $cabecalhoEmailUfs = $this->criarCabecalhoEmailUf();
        $cabecalhoEmailUfRepositoryMock = $this->createMock(CabecalhoEmailUfRepository::class);
        $cabecalhoEmailUfRepositoryMock->method('getPorCabecalhoEmail')->willReturn($cabecalhoEmailUfs);

        $historicoBO = new HistoricoBO();
        $historicoRespositoryMock = $this->createMock(HistoricoRepository::class);
        $historico = new Historico();
        $historicoRespositoryMock->method('persist')->willReturn($historico);
        $this->setPrivateProperty($historicoBO, 'historicoRepository', $historicoRespositoryMock);

        $cabecalhoEmailBO = new CabecalhoEmailBO();
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailRepository', $cabecalhoEmailRespositoryMock);
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailUfRepository', $cabecalhoEmailUfRepositoryMock);
        $this->setPrivateProperty($cabecalhoEmailBO, 'historicoBO', $historicoBO);

        $responsavel = new \stdClass();
        $responsavel->id = self::ID_RESPONSAVEL;

        $this->assertEquals($cabecalhoEmailBO->salvar($cabecalhoEmail, $responsavel), $cabecalhoEmail);
    }

    /**
     * Teste de validação do campo obrigatório em Cabeçalho E-mail.
     */
    public function testeSalvarSemTitulo()
    {
        $cabecalhoEmail = $this->criarCabecalhoEmail();
        $cabecalhoEmailRespositoryMock = $this->createMock(CabecalhoEmailRepository::class);
        $cabecalhoEmailRespositoryMock->method('persist')->willReturn($cabecalhoEmail);

        $cabecalhoEmailUfs = $this->criarCabecalhoEmailUf();
        $cabecalhoEmailUfRepositoryMock = $this->createMock(CabecalhoEmailUfRepository::class);
        $cabecalhoEmailUfRepositoryMock->method('getPorCabecalhoEmail')->willReturn($cabecalhoEmailUfs);

        $historicoBO = new HistoricoBO();
        $historicoRespositoryMock = $this->createMock(HistoricoRepository::class);
        $historico = new Historico();
        $historicoRespositoryMock->method('persist')->willReturn($historico);
        $this->setPrivateProperty($historicoBO, 'historicoRepository', $historicoRespositoryMock);

        $cabecalhoEmailBO = new CabecalhoEmailBO();
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailRepository', $cabecalhoEmailRespositoryMock);
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailUfRepository', $cabecalhoEmailUfRepositoryMock);
        $this->setPrivateProperty($cabecalhoEmailBO, 'historicoBO', $historicoBO);

        $responsavel = new \stdClass();
        $responsavel->id = self::ID_RESPONSAVEL;
        $cabecalhoEmail->setTitulo('');

        try {
            $this->assertNotEmpty($cabecalhoEmailBO->salvar($cabecalhoEmail, $responsavel));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Teste de validação do campo obrigatório em Cabeçalho E-mail.
     */
    public function testeSalvarSemCabecalhoEmailUfs()
    {
        $cabecalhoEmail = $this->criarCabecalhoEmail();
        $cabecalhoEmailRespositoryMock = $this->createMock(CabecalhoEmailRepository::class);
        $cabecalhoEmailRespositoryMock->method('persist')->willReturn($cabecalhoEmail);

        $cabecalhoEmailUfs = $this->criarCabecalhoEmailUf();
        $cabecalhoEmailUfRepositoryMock = $this->createMock(CabecalhoEmailUfRepository::class);
        $cabecalhoEmailUfRepositoryMock->method('getPorCabecalhoEmail')->willReturn($cabecalhoEmailUfs);

        $historicoBO = new HistoricoBO();
        $historicoRespositoryMock = $this->createMock(HistoricoRepository::class);
        $historico = new Historico();
        $historicoRespositoryMock->method('persist')->willReturn($historico);
        $this->setPrivateProperty($historicoBO, 'historicoRepository', $historicoRespositoryMock);

        $cabecalhoEmailBO = new CabecalhoEmailBO();
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailRepository', $cabecalhoEmailRespositoryMock);
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailUfRepository', $cabecalhoEmailUfRepositoryMock);
        $this->setPrivateProperty($cabecalhoEmailBO, 'historicoBO', $historicoBO);

        $responsavel = new \stdClass();
        $responsavel->id = self::ID_RESPONSAVEL;
        $cabecalhoEmail->setCabecalhoEmailUfs([]);

        try {
            $this->assertNotEmpty($cabecalhoEmailBO->salvar($cabecalhoEmail, $responsavel));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }
    
    /**
     * Teste de validação do campo obrigatório em Cabeçalho E-mail.
     */
    public function testeSalvarSemImagemCabecalho()
    {
        $cabecalhoEmail = $this->criarCabecalhoEmail();
        $cabecalhoEmailRespositoryMock = $this->createMock(CabecalhoEmailRepository::class);
        $cabecalhoEmailRespositoryMock->method('persist')->willReturn($cabecalhoEmail);
        
        $cabecalhoEmailUfs = $this->criarCabecalhoEmailUf();
        $cabecalhoEmailUfRepositoryMock = $this->createMock(CabecalhoEmailUfRepository::class);
        $cabecalhoEmailUfRepositoryMock->method('getPorCabecalhoEmail')->willReturn($cabecalhoEmailUfs);
        
        $historicoBO = new HistoricoBO();
        $historicoRespositoryMock = $this->createMock(HistoricoRepository::class);
        $historico = new Historico();
        $historicoRespositoryMock->method('persist')->willReturn($historico);
        $this->setPrivateProperty($historicoBO, 'historicoRepository', $historicoRespositoryMock);
        
        $cabecalhoEmailBO = new CabecalhoEmailBO();
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailRepository', $cabecalhoEmailRespositoryMock);
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailUfRepository', $cabecalhoEmailUfRepositoryMock);
        $this->setPrivateProperty($cabecalhoEmailBO, 'historicoBO', $historicoBO);
        
        $responsavel = new \stdClass();
        $responsavel->id = self::ID_RESPONSAVEL;
        $cabecalhoEmail->setImagemCabecalho(null);
        
        try {
            $this->assertNotEmpty($cabecalhoEmailBO->salvar($cabecalhoEmail, $responsavel));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }
    
    /**
     * Teste de validação do campo obrigatório em Cabeçalho E-mail.
     */
    public function testeSalvarSemImagemRodape()
    {
        $cabecalhoEmail = $this->criarCabecalhoEmail();
        $cabecalhoEmailRespositoryMock = $this->createMock(CabecalhoEmailRepository::class);
        $cabecalhoEmailRespositoryMock->method('persist')->willReturn($cabecalhoEmail);
        
        $cabecalhoEmailUfs = $this->criarCabecalhoEmailUf();
        $cabecalhoEmailUfRepositoryMock = $this->createMock(CabecalhoEmailUfRepository::class);
        $cabecalhoEmailUfRepositoryMock->method('getPorCabecalhoEmail')->willReturn($cabecalhoEmailUfs);
        
        $historicoBO = new HistoricoBO();
        $historicoRespositoryMock = $this->createMock(HistoricoRepository::class);
        $historico = new Historico();
        $historicoRespositoryMock->method('persist')->willReturn($historico);
        $this->setPrivateProperty($historicoBO, 'historicoRepository', $historicoRespositoryMock);
        
        $cabecalhoEmailBO = new CabecalhoEmailBO();
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailRepository', $cabecalhoEmailRespositoryMock);
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailUfRepository', $cabecalhoEmailUfRepositoryMock);
        $this->setPrivateProperty($cabecalhoEmailBO, 'historicoBO', $historicoBO);
        
        $responsavel = new \stdClass();
        $responsavel->id = self::ID_RESPONSAVEL;
        $cabecalhoEmail->setImagemRodape(null);
        
        try {
            $this->assertNotEmpty($cabecalhoEmailBO->salvar($cabecalhoEmail, $responsavel));
        } catch (NegocioException $e) {
            $this->assertSame($e->getMessage(), Message::$descriptions[Message::VALIDACAO_CAMPOS_OBRIGATORIOS]);
        }
    }

    /**
     * Teste de busca de UFs.
     */
    public function testeGetUfs()
    {
        $ufs = $this->criarListaUfs();
        $ufRepositoryMock = $this->createMock(UfRepository::class);
        $ufRepositoryMock->method('findAll')->willReturn($ufs);

        $cabecalhoEmailBO = new CabecalhoEmailBO();
        $this->setPrivateProperty($cabecalhoEmailBO, 'ufRepository', $ufRepositoryMock);

        $this->assertEquals($cabecalhoEmailBO->getUfs(), $ufs);
    }

    /**
     * Teste de método que retorna total de Corpo E-mail.
     */
    public function testeGetTotalCorpoEmailVinculado()
    {
        $cabecalhoEmailRespositoryMock = $this->createMock(CabecalhoEmailRepository::class);
        $cabecalhoEmailRespositoryMock->method('getTotalCorpoEmailVinculado')->willReturn(1);

        $cabecalhoEmailBO = new CabecalhoEmailBO();
        $this->setPrivateProperty($cabecalhoEmailBO, 'cabecalhoEmailRepository', $cabecalhoEmailRespositoryMock);

        $this->assertEquals($cabecalhoEmailBO->getTotalCorpoEmailVinculado(self::ID_CABECALHO_EMAIL), 1);
    }

    /**
     * Retorna lista de entidades de Cabeçalho de E-mail.
     *
     * @return array
     */
    private function criarListaCabecalhosEmail()
    {
        $cabecalhosEmail = [];
        for ($i = 0; $i <= 2; $i ++) {
            array_push($cabecalhosEmail, $this->criarCabecalhoEmail($i));
        }
        return $cabecalhosEmail;
    }

    /**
     * Retorna entidade de Cabeçalho de E-mail.
     *
     * @param integer $idCabecalhoEmail
     * @return \App\Entities\CabecalhoEmail
     */
    private function criarCabecalhoEmail($idCabecalhoEmail = null)
    {
        $cabecalhoEmail = new CabecalhoEmail();
        if (empty($idCabecalhoEmail)) {
            $cabecalhoEmail->setId(self::ID_CABECALHO_EMAIL);
        } else {
            $cabecalhoEmail->setId($idCabecalhoEmail);
        }
        $cabecalhoEmail->setTitulo(self::TITULO_CABECALHO_EMAIL);
        $cabecalhoEmail->setNomeImagemCabecalho(self::NOME_IMG_CABECALHO);
        $cabecalhoEmail->setNomeImagemRodape(self::NOME_IMG_RODAPE);
        $cabecalhoEmail->setImagemCabecalho(self::IMG_CABECALHO_RODAPE_BASE64);
        $cabecalhoEmail->setImagemRodape(self::IMG_CABECALHO_RODAPE_BASE64);
        $cabecalhoUfs = $this->criarCabecalhoEmailUf();
        $cabecalhoEmail->setCabecalhoEmailUfs($cabecalhoUfs);
        $cabecalhoEmail->definirAtivo(true);
        $cabecalhoEmail->setIsCabecalhoAtivo(true);
        $cabecalhoEmail->setIsRodapeAtivo(true);

        return $cabecalhoEmail;
    }

    /**
     * Cria entidade de CAUUF.
     *
     * @return array
     */
    private function criarCabecalhoEmailUf()
    {
        $cabecalhoUfs = [];
        for ($i = 0; $i <= 2; $i ++) {
            $cabecalhoUf = new CabecalhoEmailUf();
            $uf = $this->criarUf(self::ID_UFS[$i], self::DESCRICAO_UFS[$i]);
            $cabecalhoUf->setId($i);
            $cabecalhoUf->setUf($uf);
            array_push($cabecalhoUfs, $cabecalhoUf);
        }
        return $cabecalhoUfs;
    }

    /**
     * Retorna entidade de UF.
     *
     * @param integer $id
     * @param integer $descricao
     * @return \App\Entities\Uf
     */
    private function criarUf($id, $descricao = null)
    {
        $uf = new Uf();
        $uf->setId($id);
        $uf->setSgUf($descricao);
        return $uf;
    }

    /**
     * Retorna lista de entidades UFs.
     *
     * @return array
     */
    private function criarListaUfs()
    {
        $ufs = [];
        for ($i = 0; $i <= 2; $i ++) {
            $uf = $this->criarUf(self::ID_UFS[$i], self::DESCRICAO_UFS[$i]);
            array_push($ufs, $uf);
        }
        return $ufs;
    }
}

