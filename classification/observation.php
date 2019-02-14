<?php
namespace ChildDevelopmentPortfolio\Classification;

use Phpml\Dataset\CsvDataset;
use Phpml\Dataset\ArrayDataset;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Metric\Accuracy;
use Phpml\Classification\NaiveBayes;
use Phpml\SupportVectorMachine\Kernel;

class ObservationClassifier {
    protected $datasetFilename;

    public function __construct($datasetFilename)
    {
        $this->datasetFilename = $datasetFilename;
    }

    public function Classify($observation) {
        $dataset = new CsvDataset($this->datasetFilename, 1);
        
        $samples = [];
        foreach ($dataset->getSamples() as $sample) {
            $samples[] = $sample[0];
        }
        
        $vectorizer = new TokenCountVectorizer(new WordTokenizer());
        $vectorizer->fit($samples);
        $vectorizer->transform($samples);
        
        $tfIdfTransformer = new TfIdfTransformer($samples);
        $tfIdfTransformer->fit($samples);
        $tfIdfTransformer->transform($samples);
        
        $dataset = new ArrayDataset($samples, $dataset->getTargets());
        
        $classifier = new NaiveBayes();
        $classifier->train($samples, $dataset->getTargets());
        
        $testData = explode (' ', $observation);
        $vectorizer->fit($testData);
        $vectorizer->transform($testData);
        
        $predictedLabels = $classifier->predict($testData);

        $outcomesCounts = array_count_values($predictedLabels);
        $result = array(
            'Recommended Outcome' => $this->GetRecommendedObservation($outcomesCounts),
            'Token Breakdown' => array(
                'Unknown Tokens' => $this->GetObservationCount('0', $outcomesCounts),
                'Children have a strong sense of identity' => $this->GetObservationCount('1', $outcomesCounts),
                'Children are connected with and contribute to their world' => $this->GetObservationCount('2', $outcomesCounts),
                'Children have a strong sense of wellbeing' => $this->GetObservationCount('3', $outcomesCounts),
                'Children are confident and involved learners' => $this->GetObservationCount('4', $outcomesCounts),
                'Children are effective communicators' => $this->GetObservationCount('5', $outcomesCounts),
            )
        );

        return $result;
    }

    function GetRecommendedObservation($outcomesCounts) {
        $maxTokens = array_keys($outcomesCounts, max($outcomesCounts));
        if ($maxTokens[0] === '0' && count($outcomesCounts) === 1)
            return "None";
        
        $mostMatched = $maxTokens[0] === '0' ? $maxTokens[1] : $maxTokens[0];
        switch ($mostMatched) {
            case '1':
                return 'Children have a strong sense of identity';
            case '2':
                return 'Children are connected with and contribute to their world';
            case '3':
                return 'Children have a strong sense of wellbeing';
            case '4':
                return 'Children are confident and involved learners';
            case '5':
                return 'Children are effective communicators';
        }
    }

    function GetObservationCount($observationNumber, $outcomesCounts) {
        return array_key_exists($observationNumber, $outcomesCounts) ? $outcomesCounts[$observationNumber] : 0;
    }
}