<?php
$width  = (int) shell_exec("tput cols");
$height = (int) shell_exec("tput lines");
ncurses_init();
ncurses_timeout(0); 
ncurses_curs_set(0);
//ncurses_curs_set(1);
ncurses_cbreak();
ncurses_start_color();
ncurses_init_pair(1,NCURSES_COLOR_RED,NCURSES_COLOR_BLACK);
$window = ncurses_newwin($height, $window, 0, 0);

$cursor='#===#';
$clearCursor = '     ';
$xPos = 100;
$lastXPos = $xPos;
$go = true;
$step = 4;
//function move($direction)
while($go){
    $ch = ncurses_getch();
    switch( $ch ){
        case FALSE:
        break;
        case NCURSES_KEY_LEFT:
        if ($xPos - $step < 0){
            ncurses_flash();
            ncurses_beep();
            $xPos=0;
        }
        else
            $xPos-=$step;
            
        break;
        case NCURSES_KEY_RIGHT:
        if ($xPos + $step > $width - ($step)){
              ncurses_flash();
              ncurses_beep();
        }
        else
            $xPos+=$step;
        break;
    }
    ncurses_wcolor_set($window, 1);
    ncurses_mvwaddstr ($window , $height-1 , $lastXPos , $clearCursor);
    ncurses_mvwaddstr ($window , $height-1 , $xPos , $cursor);
    $lastXPos = $xPos;
    ncurses_wrefresh($window);
} 



