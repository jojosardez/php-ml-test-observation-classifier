<?php
namespace ChildDevelopmentPortfolio\Classification;

use ChildDevelopmentPortfolio\Utilities\WordUtilities;
use Phpml\Dataset\CsvDataset;
use Phpml\Dataset\ArrayDataset;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Metric\Accuracy;
use Phpml\Classification\NaiveBayes;
use Phpml\SupportVectorMachine\Kernel;

require __DIR__ . '../../utilities/wordutilities.php';

class ObservationClassifier {
    protected $wordUtilities;

    public function __construct()
    {
        $this->wordUtilities = new WordUtilities();
    }

    public function Classify($observation) {
        $dataset = new CsvDataset(__DIR__ . '../../datasets/observations.csv', 1);
        
        $samples = [];
        foreach ($dataset->getSamples() as $sample) {
            $samples[] = $this->wordUtilities->CleanupWords($sample[0]);
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
        
        $observation = $this->wordUtilities->CleanupWords($observation);
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
        $mostMatched = '0';
        $lastMax = 0;
        foreach ($outcomesCounts as $key => $value) {
            if ($key != '0' && $value > $lastMax) {
                $mostMatched = $key;
                $lastMax = $value;
            }
        }

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
        return "None";
    }

    function GetObservationCount($observationNumber, $outcomesCounts) {
        return array_key_exists($observationNumber, $outcomesCounts) ? $outcomesCounts[$observationNumber] : 0;
    }
}