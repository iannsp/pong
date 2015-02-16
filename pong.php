<?php
$width  = (int) shell_exec("tput cols");
$height = (int) shell_exec("tput lines");
ncurses_init();
ncurses_timeout(0); 
//ncurses_curs_set(0);
//ncurses_curs_set(2);
ncurses_cbreak();
ncurses_start_color();
ncurses_init_pair(1,NCURSES_COLOR_RED,NCURSES_COLOR_BLACK);
$window = ncurses_newwin($height, $width, 0, 0);

function playerRender($player, $vertical, $move)
{
    switch( $move ){
        case FALSE:
        break;
        case NCURSES_KEY_UP:
        if ($yPos - $step < 0){
            $yPos=0;
        }
        else
            $yPos-=$step;
            
        break;
        case NCURSES_KEY_DOWN:
        if ($yPos + $step > $height - ($step)){
/*
              ncurses_flash();
              ncurses_beep();
*/
        }
        else
            $yPos+=$step;
        break;
    }
    ncurses_wcolor_set($window, 1);
    ncurses_mvwaddstr ($window , $lastYPos , 0 , $clearCursor);
    ncurses_mvwaddstr ($window , $yPos , 0 , $cursor);
    $lastYPos = $yPos;
    ncurses_wrefresh($window);    
}

$player = ['position'=>['x'=>0,'y'=>$height],'step'=>4];
$players= ['client'=>$players,'computer'=>$player ];
$players['computer']['position']['x'] =$width;

$cursor="#\n|\n|\n#";
$clearCursor = " \n \n \n ";
$yPos = 1;
$lastYPos = $yPos;
$go = true;
$step = 4;
$bolinha = [
    'position'    => ['x'=>$width/2, 'y'=>$height/2], 
    'constraint'  => ['x'=>$width, 'y'=>$height], 
    'direction'   => ['x'=>1,'y'=>1]
];
//function colision($bolinha)
function bolinha($window, &$bolinha)
{
    ncurses_mvwaddstr ($window , 
            $bolinha['position']['y'] , 
            $bolinha['position']['x'] ,
     " ");
    $playerCorner = 1;
    $x = $bolinha['position']['x'];
    $y = $bolinha['position']['y'];
    $maxWidth  = $bolinha['constraint']['x'];
    $maxHeight = $bolinha['constraint']['y'];
    $direction = $bolinha['direction'];
    $nextX = $x + $direction['x'];
    $nextY = $y + $direction['y'];

    $bolinha['position']['x']= $x + $direction['x'];
    $bolinha['position']['y']= $y + $direction['y'];
    if ($nextX > $maxWidth-$playerCorner || $nextX < 0+$playerCorner){
        $direction['x'] = ($direction['x']) * -1;
    }
    if ($nextY > $maxHeight || $nextY < 0){
        $direction['y'] = ($direction['y']) * -1;
    }

    $bolinha['direction'] = $direction;
    ncurses_mvwaddstr ($window , 
            $bolinha['position']['y'] , 
            $bolinha['position']['x'] ,
     "#");
}

while($go){
    bolinha($window, $bolinha);
    usleep(50000);//1sec/10=>100000
    $ch = ncurses_getch();
    switch( $ch ){
        case FALSE:
        break;
        case NCURSES_KEY_UP:
        if ($yPos - $step < 0){
/*
            ncurses_flash();
            ncurses_beep();
*/
            $yPos=0;
        }
        else
            $yPos-=$step;
            
        break;
        case NCURSES_KEY_DOWN:
        if ($yPos + $step > $height - ($step)){
/*
              ncurses_flash();
              ncurses_beep();
*/
        }
        else
            $yPos+=$step;
        break;
    }
    ncurses_wcolor_set($window, 1);
    ncurses_mvwaddstr ($window , $lastYPos , 0 , $clearCursor);
    ncurses_mvwaddstr ($window , $yPos , 0 , $cursor);
    $lastYPos = $yPos;
    ncurses_wrefresh($window);
} 

