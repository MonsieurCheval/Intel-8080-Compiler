<?php


// This is a simple 8080 assembler that translates assembly code to machine code.
include_once './translator.php';

//OPTIONS
$debugmode = true;

//GET OPTIONS
// This is a simple command line option parser

//$doWeDebug = ["option:"];
//if(isset($doWeDebug)){
//    $options = getopt("", $doWeDebug);
//    $options = $options['option'];
//    $debugmode = true;
//    echo "Debugging mode: " . $options;
//}


//WELCOME MESSAGE
echo " \n\n\e[30;47m Welcome to Tondamassembler  \e[0m\n";


//FETCH THE FILE
$filename       = "assembly.asm";
$filecontent    = "";

// Check if the file exists
if (file_exists($filename)) {
    $filecontent = file_get_contents($filename);
} else {
    echo "\n \e[31;40mERROR File not found\e[0m";
    echo "\n Please create a file named 'assembly.asm' with your assembly code.";
}


$whatWeOutput = ["output:"];
if(isset($whatWeOutput)){

    $option = getopt("", $whatWeOutput);
    $option = $option['output'];

    if($option == "hex"){
        $debugmode = false;
        echo "Output mode: " . $option;
    }
}



echo assemble8080($filecontent);

























function assemble8080($assemblyCode) {

$lines = explode("\n", strtoupper($assemblyCode));
$output = [];
$output[] = "00";// start with 00

foreach ($lines as $line) {

        $line = trim(preg_replace('/;.*$/', '', $line)); // Remove comments
        if ($line === '') continue; //if empty line, skip


//A- COMMAND INSTRUCTIONS

    //1 INPUT/OUTPUT INSTRUCTION

        // IN
            // Example: IN 01
            if (preg_match('/IN ([0-9A-F]{2})/', $line, $m)) {
                $port = $m[1];
                $output[] = 'DB ' . $port;
            }
        // OUT
            // Example: OUT 01
            elseif (preg_match('/OUT ([0-9A-F]{2})/', $line, $m)) {
                $port = $m[1];
                $output[] = 'D3 ' . $port;
            }


    //2  INTERRUPT INSTRUCTION

        // EI
            elseif ($line === "EI")
                $output[] = 'FB';

        //DI
            elseif ($line === 'DI')
                $output[] = 'F3';

        // HLT
            elseif ($line === 'HLT')
              $output[] = '76';

        // RST
            // Example: RST 1
            elseif (preg_match('/RST ([0-7])/', $line, $m)) {
                $n = intval($m[1]);
                $opcode = 0xC7 + ($n << 3);
                $output[] = strtoupper(str_pad(dechex($opcode), 2, '0', STR_PAD_LEFT));
            }


    //3 CARRY BIT INSTRUCTIONS

            // CMC
                elseif ($line === 'CMC')
                    $output[] = '3F';
            // STC
                elseif ($line === 'STC')
                    $output[] = '37';


    //4 NO OPERATION INSTRUCTION

            // NOP
                elseif ($line === 'NOP')
                    $output[] = '00';

//B- SINGLE REGISTER INSTRUCTION
            // INR
                // Example: INR A
                elseif (preg_match('/INR (A|B|C)/', $line, $m)) {
                    $reg = $m[1];
                    $opcodeMap = ['A' => '3C', 'B' => '04', 'C' => '0C'];
                    $output[] = $opcodeMap[$reg];
                }
            // DCR
                // Example: DCR A
                elseif (preg_match('/DCR (A|B|C)/', $line, $m)) {
                    $reg = $m[1];
                    $opcodeMap = ['A' => '3D', 'B' => '05', 'C' => '0D'];
                    $output[] = $opcodeMap[$reg];
                }
            // CMA
                // Example: CMA
                elseif ($line === 'CMA') {
                    $output[] = '2F';
                }
            // DAA
                // Example: DAA
                elseif ($line === 'DAA') {
                    $output[] = '27';
                }

//C- REGISTER PAIR INSTRUCTIONS
        // PUSH
            // Example: PUSH B
            elseif (preg_match('/PUSH (B|D|H|PSW)/', $line, $m)) {
                $opcodeMap = ['B' => 'C5', 'D' => 'D5', 'H' => 'E5', 'PSW' => 'F5'];
                $reg = $m[1];
                $output[] = $opcodeMap[$reg];
            }
        // POP
            // Example: POP B
            elseif (preg_match('/POP (B|D|H|PSW)/', $line, $m)) {
                $opcodeMap = ['B' => 'C1', 'D' => 'D1', 'H' => 'E1', 'PSW' => 'F1'];
                $reg = $m[1];
                $output[] = $opcodeMap[$reg];
            }
        // DAD
            // Example: DAD B
            elseif (preg_match('/DAD (B|D|H|SP)/', $line, $m)) {
                $opcodeMap = ['B' => '09', 'D' => '19', 'H' => '29', 'SP' => '39'];
                $reg = $m[1];
                $output[] = $opcodeMap[$reg];
            }
        // INX
            // Example: INX B
            elseif (preg_match('/INX (B|D|H|SP)/', $line, $m)) {
                $opcodeMap = ['B' => '03', 'D' => '13', 'H' => '23', 'SP' => '33'];
                $reg = $m[1];
                $output[] = $opcodeMap[$reg];
            }
        // DCX
            // Example: DCX B
            elseif (preg_match('/DCX (B|D|H|SP)/', $line, $m)) {
                $opcodeMap = ['B' => '0B', 'D' => '1B', 'H' => '2B', 'SP' => '3B'];
                $reg = $m[1];
                $output[] = $opcodeMap[$reg];
            }
        // XCHG
            // Example: XCHG
            elseif ($line === 'XCHG') {
                $output[] = 'EB';
            }
        // XTHL
            // Example: XTHL
            elseif ($line === 'XTHL') {
                $output[] = 'E3';
            }
        // SPHL
            // Example: SPHL
            elseif ($line === 'SPHL') {
                $output[] = 'F9';
            }


//D- ROTATE ACCUMULATOR INSTRUCTIONS

        // RLC
            elseif ($line === 'RLC') {
                $output[] = '07';
            }
        // RRC
            // Example: RRC
            elseif ($line === 'RRC') {
                $output[] = '0F';
            }
        // RAL
            // Example: RAL
            elseif ($line === 'RAL') {
                $output[] = '17';
            }
        // RAR
            // Example: RAR
            elseif ($line === 'RAR') {
                $output[] = '1F';
            }



//E- DATA TRANSFER INSTRUCTIONS
    //1. Data Transfer Instructions
        // MOV
            // Example: MOV A,B or MOV M,A
            elseif (preg_match('/MOV (A|B|C|D|E|H|L|M),(A|B|C|D|E|H|L|M)/', $line, $m)) {
                $regCodes = [
                    'B' => 0, 'C' => 1, 'D' => 2, 'E' => 3,
                    'H' => 4, 'L' => 5, 'M' => 6, 'A' => 7
                ];
                $dst = $regCodes[$m[1]];
                $src = $regCodes[$m[2]];
                $opcode = 0x40 | ($dst << 3) | $src;
                $output[] = strtoupper(str_pad(dechex($opcode), 2, '0', STR_PAD_LEFT));
            }
        // STAX
            // Example: STAX B
            elseif (preg_match('/STAX (B|D)/', $line, $m)) {
                $opcodeMap = ['B' => '02', 'D' => '12'];
                $reg = $m[1];
                $output[] = $opcodeMap[$reg];
            }
        // LDAX
            // Example: LDAX B
            elseif (preg_match('/LDAX (B|D)/', $line, $m)) {
                $opcodeMap = ['B' => '0A', 'D' => '1A'];
                $reg = $m[1];
                $output[] = $opcodeMap[$reg];
            }



    //2. Register/Memory to Accumulator Transfers
        // ADD
            // Example: ADD B
            elseif (preg_match('/ADD (A|B|C|D|E|H|L|M)/', $line, $m)) {
                $regCodes = [
                    'B' => 0, 'C' => 1, 'D' => 2, 'E' => 3,
                    'H' => 4, 'L' => 5, 'M' => 6, 'A' => 7
                ];
                $src = $regCodes[$m[1]];
                $opcode = 0x80 | $src;
                $output[] = strtoupper(str_pad(dechex($opcode), 2, '0', STR_PAD_LEFT));
            }
        //ADC
            // Example: ADC B
            elseif (preg_match('/ADC (A|B|C|D|E|H|L|M)/', $line, $m)) {
                $regCodes = [
                    'B' => 0, 'C' => 1, 'D' => 2, 'E' => 3,
                    'H' => 4, 'L' => 5, 'M' => 6, 'A' => 7
                ];
                $src = $regCodes[$m[1]];
                $opcode = 0x88 | $src;
                $output[] = strtoupper(str_pad(dechex($opcode), 2, '0', STR_PAD_LEFT));
            }

        // SUB
            // Example: SUB B
            elseif (preg_match('/SUB (A|B|C|D|E|H|L|M)/', $line, $m)) {
                $regCodes = [
                    'B' => 0, 'C' => 1, 'D' => 2, 'E' => 3,
                    'H' => 4, 'L' => 5, 'M' => 6, 'A' => 7
                ];
                $src = $regCodes[$m[1]];
                $opcode = 0x90 | $src;
                $output[] = strtoupper(str_pad(dechex($opcode), 2, '0', STR_PAD_LEFT));
            }
        // SBB
            // Example: SBB B
            elseif (preg_match('/SBB (A|B|C|D|E|H|L|M)/', $line, $m)) {
                $regCodes = [
                    'B' => 0, 'C' => 1, 'D' => 2, 'E' => 3,
                    'H' => 4, 'L' => 5, 'M' => 6, 'A' => 7
                ];
                $src = $regCodes[$m[1]];
                $opcode = 0x98 | $src;
                $output[] = strtoupper(str_pad(dechex($opcode), 2, '0', STR_PAD_LEFT));
            }
        // ANA
            // Example: ANA B
            elseif (preg_match('/ANA (A|B|C|D|E|H|L|M)/', $line, $m)) {
                $regCodes = [
                    'B' => 0, 'C' => 1, 'D' => 2, 'E' => 3,
                    'H' => 4, 'L' => 5, 'M' => 6, 'A' => 7
                ];
                $src = $regCodes[$m[1]];
                $opcode = 0xA0 | $src;
                $output[] = strtoupper(str_pad(dechex($opcode), 2, '0', STR_PAD_LEFT));
            }
        // XRA
            // Example: XRA B
            elseif (preg_match('/XRA (A|B|C|D|E|H|L|M)/', $line, $m)) {
                $regCodes = [
                    'B' => 0, 'C' => 1, 'D' => 2, 'E' => 3,
                    'H' => 4, 'L' => 5, 'M' => 6, 'A' => 7
                ];
                $src = $regCodes[$m[1]];
                $opcode = 0xA8 | $src;
                $output[] = strtoupper(str_pad(dechex($opcode), 2, '0', STR_PAD_LEFT));
            }
        // ORA
            // Example: ORA B
            elseif (preg_match('/ORA (A|B|C|D|E|H|L|M)/', $line, $m)) {
                $regCodes = [
                    'B' => 0, 'C' => 1, 'D' => 2, 'E' => 3,
                    'H' => 4, 'L' => 5, 'M' => 6, 'A' => 7
                ];
                $src = $regCodes[$m[1]];
                $opcode = 0xB0 | $src;
                $output[] = strtoupper(str_pad(dechex($opcode), 2, '0', STR_PAD_LEFT));
            }
        // CMP
            // Example: CMP B
            elseif (preg_match('/CMP (A|B|C|D|E|H|L|M)/', $line, $m)) {
                $regCodes = [
                    'B' => 0, 'C' => 1, 'D' => 2, 'E' => 3,
                    'H' => 4, 'L' => 5, 'M' => 6, 'A' => 7
                ];
                $src = $regCodes[$m[1]];
                $opcode = 0xB8 | $src;
                $output[] = strtoupper(str_pad(dechex($opcode), 2, '0', STR_PAD_LEFT));
            }

    //3. Direct Addressing Instructions

        // STA
            // Example: STA 1234
            elseif (preg_match('/STA ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = '32 ' . $low . ' ' . $high;
            }
        // LDA
            // Example: LDA 1234
            elseif (preg_match('/LDA ([0-9A-F]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = '3A ' . $low . ' ' . $high;
            }
        // SHLD
            // Example: SHLD 1234
            elseif (preg_match('/SHLD ([0-9A-F]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = '22 ' . $low . ' ' . $high;
            }
        // LHLD
            // Example: LHLD 1234
            elseif (preg_match('/LHLD ([0-9A-F]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = '2A ' . $low . ' ' . $high;
            }

//F- IMMEDIATE INSTRUCTIONS

        // LXI
            // Example: LXI B,1234
            elseif (preg_match('/LXI (B|D|H|SP),([0-9A-Fa-f]{4})/', $line, $m)) {
                $opcodeMap = ['B' => '01', 'D' => '11', 'H' => '21', 'SP' => '31'];
                $reg = $m[1];
                $addr = strtoupper($m[2]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = $opcodeMap[$reg] . ' ' . $low . ' ' . $high;
            }
        // MVI
            // Example: MVI A,12
            elseif (preg_match('/MVI (A|B|C|D|E|H|L|M),([0-9A-Fa-f]{2})/', $line, $m)) {
                $regCodes = [
                    'B' => 0, 'C' => 1, 'D' => 2, 'E' => 3,
                    'H' => 4, 'L' => 5, 'M' => 6, 'A' => 7
                ];
                $reg = $regCodes[$m[1]];
                $data = strtoupper($m[2]);
                $opcode = 0x06 | ($reg << 3);
                $output[] = strtoupper(str_pad(dechex($opcode), 2, '0', STR_PAD_LEFT)) . ' ' . $data;
            }
        // ADI
            // Example: ADI 34
            elseif (preg_match('/ADI ([0-9A-Fa-f]{2})/', $line, $m)) {
                $data = strtoupper($m[1]);
                $output[] = 'C6 ' . $data;
            }
        // ACI
            // Example: ACI 34
            elseif (preg_match('/ACI ([0-9A-Fa-f]{2})/', $line, $m)) {
                $data = strtoupper($m[1]);
                $output[] = 'CE ' . $data;
            }
        // SUI
            // Example: SUI 34
            elseif (preg_match('/SUI ([0-9A-Fa-f]{2})/', $line, $m)) {
                $data = strtoupper($m[1]);
                $output[] = 'D6 ' . $data;
            }
        // SBI
            // Example: SBI 34
            elseif (preg_match('/SBI ([0-9A-Fa-f]{2})/', $line, $m)) {
                $data = strtoupper($m[1]);
                $output[] = 'DE ' . $data;
            }
        // ANI
            // Example: ANI 34
            elseif (preg_match('/ANI ([0-9A-Fa-f]{2})/', $line, $m)) {
                $data = strtoupper($m[1]);
                $output[] = 'E6 ' . $data;
            }
        // XRI
            // Example: XRI 34
            elseif (preg_match('/XRI ([0-9A-Fa-f]{2})/', $line, $m)) {
                $data = strtoupper($m[1]);
                $output[] = 'EE ' . $data;
            }
        // ORI
            // Example: ORI 34
            elseif (preg_match('/ORI ([0-9A-Fa-f]{2})/', $line, $m)) {
                $data = strtoupper($m[1]);
                $output[] = 'F6 ' . $data;
            }
        // CPI
            // Example: CPI 34
            elseif (preg_match('/CPI ([0-9A-Fa-f]{2})/', $line, $m)) {
                $data = strtoupper($m[1]);
                $output[] = 'FE ' . $data;
            }

//G- BRANCHING INSTRUCTIONS

    //1. Jump Instructions
        // PCHL
            // Example: PCHL
            elseif ($line === 'PCHL') {
                $output[] = 'E9';
            }
        // JMP
            // Example: JMP 1234
            elseif (preg_match('/JMP ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'C3 ' . $low . ' ' . $high;
            }
        // JC
            // Example: JC 1234
            elseif (preg_match('/JC ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'DA ' . $low . ' ' . $high;
            }
        // JNC
            // Example: JNC 1234
            elseif (preg_match('/JNC ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'D2 ' . $low . ' ' . $high;
            }
        // JZ
            // Example: JZ 1234
            elseif (preg_match('/JZ ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'CA ' . $low . ' ' . $high;
            }
        // JNZ
            // Example: JNZ 1234
            elseif (preg_match('/JNZ ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'C2 ' . $low . ' ' . $high;
            }

        // JM
            // Example: JM 1234
            elseif (preg_match('/JM ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'FA ' . $low . ' ' . $high;
            }
        // JP
            // Example: JP 1234
            elseif (preg_match('/JP ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'F2 ' . $low . ' ' . $high;
            }
        // JPE
            // Example: JPE 1234
            elseif (preg_match('/JPE ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'EA ' . $low . ' ' . $high;
            }
        // JPO
            // Example: JPO 1234
            elseif (preg_match('/JPO ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'E2 ' . $low . ' ' . $high;
            }

    //2. Call Instructions

        // CALL
            // Example: CALL 1234
            elseif (preg_match('/CALL ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'CD ' . $low . ' ' . $high;
            }

        // CC
            // Example: CC 1234
            elseif (preg_match('/CC ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'DC ' . $low . ' ' . $high;
            }

        // CNC
            // Example: CNC 1234
            elseif (preg_match('/CNC ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'D4 ' . $low . ' ' . $high;
            }
        // CZ
            // Example: CZ 1234
            elseif (preg_match('/CZ ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'CC ' . $low . ' ' . $high;
            }
        // CNZ
            // Example: CNZ 1234
            elseif (preg_match('/CNZ ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'C4 ' . $low . ' ' . $high;
            }
        // CM
            // Example: CM 1234
            elseif (preg_match('/CM ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'FC ' . $low . ' ' . $high;
            }
        // CP
            // Example: CP 1234
            elseif (preg_match('/CP ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'F4 ' . $low . ' ' . $high;
            }
        // CPE
            // Example: CPE 1234
            elseif (preg_match('/CPE ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'EC ' . $low . ' ' . $high;
            }
        // CPO
            // Example: CPO 1234
            elseif (preg_match('/CPO ([0-9A-Fa-f]{4})/', $line, $m)) {
                $addr = strtoupper($m[1]);
                $low = substr($addr, 2, 2);
                $high = substr($addr, 0, 2);
                $output[] = 'E4 ' . $low . ' ' . $high;
            }
    //3. Return Instructions

        // RET
            // Example: RET
            elseif ($line === 'RET') {
                $output[] = 'C9';
            }
        // RC
            // Example: RC
            elseif ($line === 'RC') {
                $output[] = 'D8';
            }
        // RNC
            // Example: RNC
            elseif ($line === 'RNC') {
                $output[] = 'D0';
            }
        // RZ
            // Example: RZ
            elseif ($line === 'RZ') {
                $output[] = 'C8';
            }
        // RNZ
            // Example: RNZ
            elseif ($line === 'RNZ') {
                $output[] = 'C0';
            }
        // RM
            // Example: RM
            elseif ($line === 'RM') {
                $output[] = 'F8';
            }
        // RP
            // Example: RP
            elseif ($line === 'RP') {
                $output[] = 'F0';
            }
        // RPE
            // Example: RPE
            elseif ($line === 'RPE') {
                $output[] = 'E8';
            }
        // RPO
            // Example: RPO
            elseif ($line === 'RPO') {
                $output[] = 'E0';
            }





        else {
            $output[] = '?? ; Unrecognized: ' . $line;
        }
    }

    echo "\n\n";
    echo (implode("\n", $output));
}

echo "\n"; //just more spaces
