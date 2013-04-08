<?php
/**
 *  ласс получени€ основы (корн€) слова.
 * Based on Lingua_Stem_Ru
 * 
 * @author enikeishik
 *
 */
class UfoSearchStemmer
{
    const VOWEL = '/аеиоуыэю€/u';
    const PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[а€])(в|вши|вшись)))$/';
    const REFLEXIVE = '/(с[€ь])$/';
    const ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|ему|ому|их|ых|ую|юю|а€|€€|ою|ею)$/';
    const PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[а€])(ем|нн|вш|ющ|щ)))$/';
    const VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ен|ило|ыло|ено|€т|ует|уют|ит|ыт|ены|ить|ыть|ишь|ую|ю)|((?<=[а€])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/';
    const NOUN = '/(а|ев|ов|ие|ье|е|и€ми|€ми|ами|еи|ии|и|ией|ей|ой|ий|й|и€м|€м|ием|ем|ам|ом|о|у|ах|и€х|€х|ы|ь|ию|ью|ю|и€|ь€|€)$/';
    const RVRE = '/^(.*?[аеиоуыэю€])(.*)$/';
    const DERIVATIONAL = '/[^аеиоуыэю€][аеиоуыэю€]+[^аеиоуыэю€]+[аеиоуыэю€].*(?<=о)сть?$/';
    
    /**
     * ѕолучение корн€ слова.
     * @param string $word    исходное слово
     * @return string         корень слова
     */
    public function stem($word)
    {
        $word = mb_strtolower($word);
        $word = str_replace('Є', 'е', $word);
        $stem = $word;

        do {
            if (!preg_match(self::RVRE, $word, $p)) {
                break;
            }
            $start = $p[1];
            $rv = $p[2];
            if (!$rv) {
                break;
            }
            
            # Step 1
            if (!$this->r($rv, self::PERFECTIVEGROUND, '')) {
                $this->r($rv, self::REFLEXIVE, '');
                if ($this->r($rv, self::ADJECTIVE, '')) {
                    $this->r($rv, self::PARTICIPLE, '');
                } else if (!$this->r($rv, self::VERB, '')) {
                    $this->r($rv, self::NOUN, '');
                }
            }
            
            # Step 2
            $this->r($rv, '/и$/', '');
            
            # Step 3
            if ($this->preg_match(self::DERIVATIONAL, $rv)) {
                $this->r($rv, '/ость?$/', '');
            }
            
            # Step 4
            if (!$this->r($rv, '/ь$/', '')) {
                $this->r($rv, '/ейше?/', '');
                $this->r($rv, '/нн$/', 'н');
            }
            
            $stem = $start . $rv;
        } while(false);
        
        return $stem;
    }
    
    protected function r(&$s, $re, $to)
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }
}
