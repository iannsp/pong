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

$player = ['position'=>['x'=>0,'y'=>0],'step'=>4];
$players= ['client'=>$player,'computer'=>$player];
$players['computer']['position']['x'] =$width-1;
$players['computer']['direction']=-1;

function computerPlay(&$player, $vertical){
    $move = 0;
    
    if ($player['direction']==1){
        $testMovePlayer = playerRender($player, $vertical, NCURSES_KEY_UP);
        $move = NCURSES_KEY_UP;
    }else {
        $testMovePlayer = playerRender($player, $vertical, NCURSES_KEY_DOWN);
        $move = NCURSES_KEY_DOWN;
    }
    if ($testMovePlayer['position']['y']== $vertical-4||
        $testMovePlayer['position']['y']== 0)
        $player['direction'] = $player['direction'] * -1;
    return $move;
}
function playerRender($player, $vertical, $direction)
{
    $cursorSize = strlen("#--#");
    $x = $player['position']['x'];
    $y = $player['position']['y'];
    $step = $player['step'];
    $nextMove = $y;
    if($direction==NCURSES_KEY_UP){
        $nextMove -= $step;
    }
    else if ($direction == NCURSES_KEY_DOWN){
        $nextMove += $step;
    }
    
    if($nextMove > $vertical-$cursorSize){
      $nextMove = $vertical-$cursorSize;
    } 
    else if ($nextMove < 0){
        $nextMove= 0;
    }   
    else{
        $y = $nextMove;
    }
    $player['position'] = ['x'=>$x,'y'=>$y];
    return $player;
}


$cursor=["#","|","|","#"];
$clearCursor = [" "," "," "," "];
$go = true;
$step = 4;
$bolinha = [
    'position'    => ['x'=>$width/2, 'y'=>$height/2], 
    'constraint'  => ['x'=>$width, 'y'=>$height], 
    'direction'   => ['x'=>1,'y'=>1]
];
function drawPlayer($position, $cursor, $window)
{
    foreach($cursor as $pos => $char){
        ncurses_mvwaddstr ($window , $position['y']+$pos , $position['x'] , $char);
    }
}
function colision($bolinha, $players){
    
}
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

$cicle = 11;
while($go){
    bolinha($window, $bolinha);
    ncurses_wrefresh($window);
    usleep(50000);//1sec/10=>100000
    $cicle++;    
    $move = ncurses_getch();
    foreach ($players as $name=> $player){
        $lastPlayerPos = $player['position'];
        if ($name=='computer'){
            $move = computerPlay($player, $height);
            if($cicle < 10){
                continue;
            }
            else{
                $cicle=0;
            }
        }
        else{
            if($move<0){
                continue;
            }
        }
        $player = playerRender($player, $height, $move);
        $players[$name] = $player;
        ncurses_wcolor_set($window, 1);
        drawPlayer($lastPlayerPos, $clearCursor, $window);
        drawPlayer($player['position'], $cursor, $window);

        ncurses_wrefresh($window);
    }
}
