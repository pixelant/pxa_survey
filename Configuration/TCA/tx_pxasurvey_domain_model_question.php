<?php
$ll = 'LLL:EXT:pxa_survey/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $ll . 'tx_pxasurvey_domain_model_question',
        'label' => 'text',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'sortby' => 'sorting',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'type' => 'type',

        'searchFields' => 'text,type,answers',
        'iconfile' => 'EXT:pxa_survey/Resources/Public/Icons/tx_question.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, text, type, append_with_input, required, answers',
    ],
    'types' => [
        // remove default fields to make it more compact
        '1' => ['showitem' => '--palette--;;options, text, answers']
        // remove access tab.
        //, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime
    ],
    'palettes' => [
        'options' => ['showitem' => 'type, required, append_with_input']
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_pxasurvey_domain_model_question',
                'foreign_table_where' => 'AND tx_pxasurvey_domain_model_question.pid=###CURRENT_PID### AND tx_pxasurvey_domain_model_question.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ]
            ],

        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ]
            ],
        ],
        'text' => [
            'exclude' => true,
            'label' => $ll . 'tx_pxasurvey_domain_model_question.text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'type' => [
            'exclude' => true,
            'label' => $ll . 'tx_pxasurvey_domain_model_question.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                // @codingStandardsIgnoreStart
                'items' => [
                    [$ll . 'tx_pxasurvey_domain_model_question.type.' . \Pixelant\PxaSurvey\Domain\Model\Question::ANSWER_TYPE_RADIO, \Pixelant\PxaSurvey\Domain\Model\Question::ANSWER_TYPE_RADIO],
                    [$ll . 'tx_pxasurvey_domain_model_question.type.' . \Pixelant\PxaSurvey\Domain\Model\Question::ANSWER_TYPE_CHECKBOXES, \Pixelant\PxaSurvey\Domain\Model\Question::ANSWER_TYPE_CHECKBOXES],
                    [$ll . 'tx_pxasurvey_domain_model_question.type.' . \Pixelant\PxaSurvey\Domain\Model\Question::ANSWER_TYPE_INPUT, \Pixelant\PxaSurvey\Domain\Model\Question::ANSWER_TYPE_INPUT]
                ]
                // @codingStandardsIgnoreEnd
            ]
        ],
        'append_with_input' => [
            'exclude' => true,
            'label' => $ll . 'tx_pxasurvey_domain_model_question.append_with_input',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [],
                'itemsProcFunc' => \Pixelant\PxaSurvey\UserFunction\AnswerInputTypeSelect::class . '->renderItems'
            ],
        ],
        'required' => [
            'exclude' => true,
            'label' => $ll . 'tx_pxasurvey_domain_model_question.required',
            'config' => [
                'type' => 'check',
                'items' => [
                    [$ll . 'tx_pxasurvey_domain_model_question.required.label', '1']
                ],
                'default' => 1
            ],
        ],
        'answers' => [
            'exclude' => true,
            'displayCond' => 'FIELD:type:!=:' . \Pixelant\PxaSurvey\Domain\Model\Question::ANSWER_TYPE_INPUT,
            'label' => $ll . 'tx_pxasurvey_domain_model_question.answers',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_pxasurvey_domain_model_answer',
                'foreign_field' => 'question',
                'foreign_sortby' => 'sorting',
                'maxitems' => 9999,
                'minitems' => 1,
                'appearance' => [
                    'collapseAll' => 1,
                    'levelLinksPosition' => 'both',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],
        ],
        'survey' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
