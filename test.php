<?php

require __DIR__ . '/vendor/autoload.php';


use Phpml\Dataset\CsvDataset;
use Phpml\Dataset\ArrayDataset;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Metric\Accuracy;
use Phpml\Classification\NaiveBayes;
use Phpml\SupportVectorMachine\Kernel;


$dataset = new CsvDataset('data.csv', 1);
$vectorizer = new TokenCountVectorizer(new WordTokenizer());

$samples = [];
foreach ($dataset->getSamples() as $sample) {
    $samples[] = $sample[0];
}
$vectorizer->fit($samples);
$vectorizer->transform($samples);

$tfIdfTransformer = new TfIdfTransformer($samples);
$tfIdfTransformer->fit($samples);
$tfIdfTransformer->transform($samples);

$dataset = new ArrayDataset($samples, $dataset->getTargets());

$classifier = new NaiveBayes();
$classifier->train($samples, $dataset->getTargets());

$testData = explode (' ', 'she initiate interactions and conversations with trusted educators, and begin to recognise that they have a right to belong to many communities');
$vectorizer->fit($testData);
$vectorizer->transform($testData);

$predictedLabels = $classifier->predict($testData);
var_dump($predictedLabels);

$counts = array_count_values($predictedLabels);
echo 'Unknown: '.$counts['0'].' instances, Outcome 1: '.$counts['1'].' instances, Outcome 2: '.$counts['2'].' instances';

//echo 'Accuracy: '.Accuracy::score($dataset->getTargets(), $predictedLabels);