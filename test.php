<?php

use ChildDevelopmentPortfolio\Classification\ObservationClassifier;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/classification/observation.php';

$classifier = new ObservationClassifier(__DIR__ . '/datasets/observations.csv');

$testObservation1 = 'she initiate interactions and conversations with trusted educators, and begin to recognise that they have a right to belong to many communities';
var_dump($classifier->Classify($testObservation1));

$testObservation2 = 'Sabrina is playful and is always reaching out for company and friendship';
var_dump($classifier->Classify($testObservation2));