<?php
namespace ChildDevelopmentPortfolio\Utilities;

use voku\helper\StopWords;

class WordUtilities {
    public function CleanupWords($input) {
        $input = strtolower($input);
        $input = $this->RemoveSymbols($input);
        $input = $this->RemoveStopWords($input);
        return $input;
    }

    function RemoveSymbols($input){        
        $commonSymbols = array('.', ',', '\'', '"', ':');
        $replaceValues = array_fill(0, sizeof($commonSymbols), '');
        return str_ireplace($commonSymbols, $replaceValues, $input);
    }

    function RemoveStopWords($input) {
        $stopWords = new StopWords();         
        $commonWords = $stopWords->getStopWordsFromLanguage('en');
        return preg_replace('/\b('.implode('|',$commonWords).')\b/','',$input);
   }
}