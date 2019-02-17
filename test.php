<?php

use ChildDevelopmentPortfolio\Classification\ObservationClassifier;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/classification/observation.php';

$classifier = new ObservationClassifier();

$testObservation1 = 'she initiate interactions and conversations with trusted educators, and begin to recognise that they have a right to belong to many communities';
$testObservation1Outcome = $classifier->Classify($testObservation1)["Recommended Outcome"];
echo 'Observation: "'.$testObservation1.'"'.PHP_EOL;
echo 'Recommended outcome: '.$testObservation1Outcome.PHP_EOL.PHP_EOL;

$testObservation2 = 'Sabrina is playful and is always reaching out for company and friendship';
$testObservation2Outcome = $classifier->Classify($testObservation2)["Recommended Outcome"];
echo 'Observation: "'.$testObservation2.'"'.PHP_EOL;
echo 'Recommended outcome: '.$testObservation2Outcome.PHP_EOL.PHP_EOL;

$testObservation3 = 'Sabrina communicate her needs for comfort and assistance. She is playful and is always reaching out for company and friendship. She alse express an opinion in matters that affect them.';
$testObservation3Outcome = $classifier->Classify($testObservation3)["Recommended Outcome"];
echo 'Observation: "'.$testObservation3.'"'.PHP_EOL;
echo 'Recommended outcome: '.$testObservation3Outcome.PHP_EOL.PHP_EOL;