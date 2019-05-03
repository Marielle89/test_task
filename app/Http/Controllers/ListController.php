<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Session;
use App\Word;
use App\Imports\WordsImport;
use App\Exports\WordsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\WordsList;

class ListController extends Controller
{
    private $numberTopWord = 5; /*** количество частовстерчаемых слов ***/
    private $depthTop = 4;
    public function index()
    {
        return view('list');
    }

    public function importList(Request $request)
    {
        $error = '';
        $file = $request->file('file');
        /*** Проверяем валидность загружаемого файла ***/
        $validator = Validator::make(
            [
                'file'      => $file,
                'extension' => strtolower($file->getClientOriginalExtension()),
            ],
            [
                'file'          => 'required',
                'extension'      => 'required|in:doc,csv,xlsx,xls,docx,ppt,odt,ods,odp',
            ]
        );

        if ($validator->fails() === false) {
            $wordsList = self::createWordsList($file->getClientOriginalName());
            if ($wordsList->id) {
                self::import($file, $wordsList->id);
                $data = self::createTop($wordsList->id);
                return view('list', ['list_id' => $wordsList->id, 'keyWords' => $data['keyWords'], 'top5_words' => $data['top5_words'], 'top1with2_phrases_to_export' => $data['top1with2_phrases']]);
            } else {
                $error = "Файл имеет не корректное название";
            }

           // return back();

        } else {
            $error = "Убедитесь в корректности загружаемого файла";
        }

        if (empty($error)) {

        } else {
            return view('list', compact('error'));
        }
    }

    protected function createTop($list_id)
    {
        $keyWords = Word::ListId($list_id)->get();
        $topWords = array();
        foreach ($keyWords as $keyWord) {
            $keyWord = mb_strtolower($keyWord->word);
            $words = preg_split('/\s+/', $keyWord);
            foreach ($words as $word) {
                if (mb_strlen($word) > 2) {
                    $topWords[$word][] = $keyWord;
                }
            }
        }
        array_multisort(array_map('count', $topWords), SORT_DESC, $topWords);
        $top = array_slice($topWords, 0, $this->numberTopWord, true);
        $top_words = array_keys($top);

        if (count($top_words) >= count($this->depthTop)) {
            $topWordsForPhrases = array_reverse(array_slice($top_words, 0, $this->depthTop));
            $topWordsForPhrasesSlice = $topWordsForPhrases;
        }
        $allTopPhrases = array();
        foreach ($topWordsForPhrases as $topWordsForPhrase) {
            $allPhrasesCurrentLevel = $top[$topWordsForPhrase];
            foreach ($allPhrasesCurrentLevel as $phrase) {
                $phrase_array = preg_split('/\s+/', $phrase);
                if (count(array_intersect($topWordsForPhrasesSlice, $phrase_array)) == count($topWordsForPhrasesSlice)) {
                    $allTopPhrases[$topWordsForPhrase][] = $phrase;
                }
                $phrasePosition = array_search($topWordsForPhrase, $topWordsForPhrasesSlice);
                unset($topWordsForPhrasesSlice[$phrasePosition]);
            }
        }
        $clear_array = array();

        foreach ($allTopPhrases as $key => &$allTopPhrase) {
            $allTopPhrase = array_diff($allTopPhrase, $clear_array);
            $clear_array = array_merge($clear_array, $allTopPhrase);
        }
        $allTopPhrasesTable = array();
        $numberCell = 0;
        foreach ($allTopPhrases as $key => $value) {
            $row = 0;
            foreach ($value as $cell) {
                $allTopPhrasesTable[$row][$numberCell] = $cell;
                $row++;
            }
            $numberCell++;
        }
        $data = array();
        $data['keyWords'] = $keyWords;
        $data['top5_words'] = $top_words;
        $data['top1with2_phrases'] = $allTopPhrases;
        $data['top1with2_phrases_to_export'] = $allTopPhrasesTable;
        return $data;
    }

    protected function createWordsList($name, $user_id = null)
    {
        if (empty($name)) {
            return false;
        }
        $name = md5($name . time());
        $wordsList = WordsList::create(['name'=>$name, 'user_id'=>$user_id]);
        return $wordsList;
    }

    public function import($file, $list_id)
    {
        Excel::import(new WordsImport($list_id), $file);
    }

    public function exportList($list_id)
    {
        $data = self::createTop($list_id);
        $export = new WordsExport($data['top1with2_phrases_to_export']);
        return Excel::download($export, 'list.csv', null, array('Content-Encoding' => 'UTF-8', 'Content-type' => 'text/csv; charset=UTF-8'));
    }
}
