<?php
/**
 * ����� ��������� ������ (�����) �����.
 * Based on Lingua_Stem_Ru
 * 
 * @author enikeishik
 *
 */
class UfoSearchStemmer
{
    const VOWEL = '/���������/u';
    const PERFECTIVEGROUND = '/((��|����|������|��|����|������)|((?<=[��])(�|���|�����)))$/';
    const REFLEXIVE = '/(�[��])$/';
    const ADJECTIVE = '/(��|��|��|��|���|���|��|��|��|��|��|��|��|��|���|���|���|���|��|��|��|��|��|��|��|��)$/';
    const PARTICIPLE = '/((���|���|���)|((?<=[��])(��|��|��|��|�)))$/';
    const VERB = '/((���|���|���|����|����|���|���|���|��|��|��|��|��|��|��|���|���|���|��|���|���|��|��|���|���|���|���|��|�)|((?<=[��])(��|��|���|���|��|�|�|��|�|��|��|��|��|��|��|���|���)))$/';
    const NOUN = '/(�|��|��|��|��|�|����|���|���|��|��|�|���|��|��|��|�|���|��|���|��|��|��|�|�|��|���|��|�|�|��|��|�|��|��|�)$/';
    const RVRE = '/^(.*?[���������])(.*)$/';
    const DERIVATIONAL = '/[^���������][���������]+[^���������]+[���������].*(?<=�)���?$/';
    
    /**
     * ��������� ����� �����.
     * @param string $word    �������� �����
     * @return string         ������ �����
     */
    public function stem($word)
    {
        $word = mb_strtolower($word);
        $word = str_replace('�', '�', $word);
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
            $this->r($rv, '/�$/', '');
            
            # Step 3
            if ($this->preg_match(self::DERIVATIONAL, $rv)) {
                $this->r($rv, '/����?$/', '');
            }
            
            # Step 4
            if (!$this->r($rv, '/�$/', '')) {
                $this->r($rv, '/����?/', '');
                $this->r($rv, '/��$/', '�');
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
