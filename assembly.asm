MVI A,05
STA 0080
MVI A,07
STA 0081
LDA 0080
MOV B,A
LDA 0081
ADD B
STA 0082
JMP 0000
