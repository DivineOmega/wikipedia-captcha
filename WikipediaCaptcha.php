<?php

namespace App\Helpers\WikipediaCaptcha;


use DivineOmega\WikipediaInfoBoxParser\Enums\Format;
use DivineOmega\WikipediaInfoBoxParser\WikipediaInfoBoxParser;

class WikipediaCaptcha
{
    public function getQuestion(array $pages) : Question
    {
        $page = $pages[array_rand($pages)];

        $infobox = (new WikipediaInfoBoxParser())
            ->setArticle($page)
            ->setFormat(Format::PLAIN_TEXT)
            ->parse();

        $questions = $this->mapToQuestions($page, $infobox);

        $key = array_rand($questions);
        $questionText = $questions[$key];

        $hiddenValue = encrypt([
            'page' => $page,
            'key' => $key
        ]);

        return new Question($questionText, $hiddenValue);
    }

    public function checkAnswer(string $answer, string $hiddenValue)
    {
        if (strlen($answer) <= 2) {
            return false;
        }

        $hiddenValue = decrypt($hiddenValue);

        $infobox = (new WikipediaInfoBoxParser())
            ->setArticle($hiddenValue['page'])
            ->setFormat(Format::PLAIN_TEXT)
            ->parse();

        $correctAnswer = strtolower($infobox[$hiddenValue['key']]);
        $answer = strtolower($answer);

        if (stripos($correctAnswer, $answer) !== false) {
            return true;
        }

        similar_text($correctAnswer, $answer, $percent);

        if ($percent >= 50) {
            return true;
        }

        return false;
    }

    private function mapToQuestions(string $page, array $infobox) : array
    {
        $questions = [];

        foreach($infobox as $key => $value) {

            $questionText = null;

            switch ($key) {
                case 'Old Name':
                    $questionText = 'What was '.$page.' previously known as?';
                    break;

                case 'developer':
                    $questionText = 'What person or team develops '.$page.'?';
                    break;

                case 'designer':
                    $questionText = 'Who was the designer of '.$page.'?';
                    break;

                case 'released':
                    $questionText = 'When was '.$page.' first released?';
                    break;

                case 'latest release version':
                    $questionText = 'What is the latest version of '.$page.'?';
                    break;

                case 'latest release date':
                    $questionText = 'When was the latest version of '.$page.' released?';
                    break;

                case 'license':
                    $questionText = 'Under what license is '.$page.' distributed?';
                    break;

                case 'programming language':
                    $questionText = 'What programming language is '.$page.' written in?';
                    break;

                case 'operating system':
                    $questionText = 'What operating system(s) does '.$page.' run on?';
                    break;

                case 'employees':
                    $questionText = 'Approximately how many employees does '.$page.' have?';
                    break;
            }

            if ($questionText) {
                $questions[$key] = $questionText;
            }

        }

        return $questions;
    }


}