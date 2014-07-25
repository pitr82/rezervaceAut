<?php
# Definujeme si zkratku pro rychlé dumpování do DebugBaru
function barDump($var, $title='')
{
    $backtrace = debug_backtrace();
    $source = (isset($backtrace[1]['class'])) ?
        $backtrace[1]['class'] :
        basename($backtrace[0]['file']);
    $line = $backtrace[0]['line'];
    if($title !== '')
        $title .= ' – ';
    return Nette\Diagnostics\Debugger::barDump($var, $title . $source . ' (' . $line .')');
}

