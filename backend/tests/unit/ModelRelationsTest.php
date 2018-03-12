<?php

namespace tests\unit;

use PHPUnit\Framework\TestCase;
use steroids\base\WebApplication;
use steroids\helpers\DefaultConfig;
use tests\data\models\Article;
use tests\data\models\Attachment;
use tests\data\models\Category;
use tests\data\models\Photo;
use tests\data\models\PhotoBar;
use tests\data\models\PhotoFoo;

class ModelRelationsTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        $appDir = __DIR__ . '/../data/modules';
        $namespace = 'tests\\data\\modules';
        $config = [
            'id' => 'test',
            'components' => [
                'db' => [
                    'dsn' => 'mysql:host=localhost;dbname=yii-steroids',
                    'username' => 'root',
                    'password' => '123',
                ],
            ],
        ];

        new WebApplication(DefaultConfig::getWebConfig($config, $appDir, $namespace));

        // Truncate tables
        \Yii::$app->db->createCommand('
            TRUNCATE `test_article`;
            TRUNCATE `test_article_photos`;
            TRUNCATE `test_attachments`;
            TRUNCATE `test_category`;
            TRUNCATE `test_photos`;
            TRUNCATE `test_photo_bar`;
            TRUNCATE `test_photo_foo`;
        ')->execute();
    }

    public static function tearDownAfterClass()
    {
        \Yii::$app = null;
    }

    public function testManyManyIds()
    {
        $article = new Article();
        $article->listenRelationIds('photos');

        $data = [
            'title' => 'test article',
            'photosIds' => [
                $this->createPhoto()->primaryKey,
                $this->createPhoto()->primaryKey,
            ],
        ];

        // Insert
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals($data['photosIds'], $article->photosIds);
        $this->assertEquals(false, $article->hasErrors());
        $this->assertEquals(true, $article->save());
        $this->assertEquals(2, $article->getPhotos()->count());

        // Delete
        unset($data['photosIds'][0]);
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals($data['photosIds'], $article->photosIds);
        $this->assertEquals(false, $article->hasErrors());
        $this->assertEquals(true, $article->save());
        $this->assertEquals(1, $article->getPhotos()->count());

    }

    public function testHasOneData()
    {
        $article = new Article();
        $article->listenRelationData('category');
        $titlePrefix = microtime(true);

        $this->assertEquals(0, Category::find()->where(['like', 'title', $titlePrefix])->count());

        // Insert
        $data = [
            'title' => 'test article',
            'category' => [
                'title' => $titlePrefix . ' test category',
            ],
        ];
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals(true, $article->isRelationPopulated('category'));
        $this->assertNull($article->category->id);
        $this->assertEquals(false, $article->hasErrors());
        $this->assertEquals(true, $article->save());
        $this->assertNotNull($article->category->id);
        $article->refresh();
        $this->assertNotNull($article->category->id);
        $this->assertEquals($titlePrefix . ' test category', $article->category->title);

        // Update
        $data['category']['title'] = $titlePrefix . ' new title';
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals(true, $article->isRelationPopulated('category'));
        $this->assertNotNull($article->category->id);
        $this->assertEquals(false, $article->hasErrors());
        $this->assertEquals(true, $article->save());
        $this->assertNotNull($article->category->id);
        $this->assertEquals($titlePrefix . ' new title', $article->category->title);
        $article->refresh();
        $this->assertEquals($titlePrefix . ' new title', $article->category->title);

        // Delete
        $data['category'] = null;
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals(true, $article->isRelationPopulated('category'));
        $this->assertNull($article->category);
        $this->assertNull($article->category);
        $this->assertNull($article->categoryId);
        $this->assertEquals(false, $article->hasErrors());
        $this->assertEquals(true, $article->save());
        $article->refresh();
        $this->assertNull($article->category);
        $this->assertEquals(0, $article->categoryId);
        $this->assertEquals(1, Category::find()->where(['like', 'title', $titlePrefix])->count());
    }

    public function testHasOneInverseData()
    {
        $article = new Article();
        $article->listenRelationData('file');
        $fileNamePrefix = 'file' . microtime(true);

        $this->assertEquals(0, Attachment::find()->where(['like', 'fileName', $fileNamePrefix])->count());

        // Insert
        $data = [
            'title' => 'test article',
            'file' => [
                'fileName' => $fileNamePrefix . ' test',
            ],
        ];
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals(true, $article->isRelationPopulated('file'));
        $this->assertNull($article->file->id);
        $this->assertEquals(false, $article->hasErrors());
        $this->assertEquals(true, $article->save());
        $this->assertNotNull($article->file->id);
        $article->refresh();
        $this->assertNotNull($article->file->id);
        $this->assertEquals($fileNamePrefix . ' test', $article->file->fileName);

        // Update
        $data['file']['fileName'] = $fileNamePrefix . ' new';
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals(true, $article->isRelationPopulated('file'));
        $this->assertNotNull($article->file->id);
        $this->assertEquals(false, $article->hasErrors());
        $this->assertEquals(true, $article->save());
        $this->assertNotNull($article->file->id);
        $this->assertEquals($fileNamePrefix . ' new', $article->file->fileName);
        $article->refresh();
        $this->assertEquals($fileNamePrefix . ' new', $article->file->fileName);

        // Delete
        $data['file'] = null;
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals(true, $article->isRelationPopulated('file'));
        $this->assertNull($article->file);
        $this->assertEquals(false, $article->hasErrors());
        $this->assertEquals(true, $article->save());
        $article->refresh();
        $this->assertNull($article->file);
        $this->assertEquals(1, Attachment::find()->where(['like', 'fileName', $fileNamePrefix])->count());
    }

    public function testHasManyData()
    {
        $article = new Article();
        $article->listenRelationData('attachments');
        $fileNamePrefix = 'attach' . microtime(true);

        $this->assertEquals(0, Attachment::find()->where(['like', 'fileName', $fileNamePrefix])->count());

        // Insert
        $data = [
            'title' => 'test article',
            'attachments' => [
                [
                    'fileName' => $fileNamePrefix . ' test 1',
                ],
                [
                    'fileName' => $fileNamePrefix . ' test 2',
                ],
            ],
        ];
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals(true, $article->isRelationPopulated('attachments'));
        $this->assertEquals(2, count($article->attachments));
        $this->assertEquals(false, $article->hasErrors());
        $this->assertEquals(true, $article->save());
        $this->assertNotNull($article->attachments[0]->id);
        $article->refresh();
        $this->assertEquals($fileNamePrefix . ' test 1', $article->attachments[0]->fileName);
        $this->assertEquals(2, Attachment::find()->where(['like', 'fileName', $fileNamePrefix])->count());

        // One update, one insert, one delete
        $data['attachments'] = [
            [
                'id' => $article->attachments[0]->id,
                'fileName' => $fileNamePrefix . ' test 1 UPDATED',
            ],
            [
                'fileName' => $fileNamePrefix . ' test 3',
            ],
        ];
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals(true, $article->isRelationPopulated('attachments'));
        $this->assertEquals(true, $article->save());
        $this->assertEquals($fileNamePrefix . ' test 1 UPDATED', $article->attachments[0]->fileName);
        $this->assertEquals($fileNamePrefix . ' test 3', $article->attachments[1]->fileName);
        $article->refresh();
        $this->assertEquals($fileNamePrefix . ' test 1 UPDATED', $article->attachments[0]->fileName);
        $this->assertEquals($fileNamePrefix . ' test 3', $article->attachments[1]->fileName);
        $this->assertEquals(2, Attachment::find()->where(['like', 'fileName', $fileNamePrefix])->count());

        // Delete
        $data['attachments'] = null;
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals(true, $article->save());
        $article->refresh();
        $this->assertEquals([], $article->attachments);
        $this->assertEquals(0, Attachment::find()->where(['like', 'fileName', $fileNamePrefix])->count());
    }

    public function testManyManyData()
    {
        $article = new Article();
        $article->listenRelationData('photos');
        $fileNamePrefix = 'many-attach-many' . microtime(true);

        $this->assertEquals(0, Photo::find()->where(['like', 'fileName', $fileNamePrefix])->count());

        // Insert
        $data = [
            'title' => 'test article',
            'photos' => [
                [
                    'fileName' => $fileNamePrefix . ' test 1',
                ],
                [
                    'fileName' => $fileNamePrefix . ' test 2',
                ],
            ],
        ];
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals(true, $article->isRelationPopulated('photos'));
        $this->assertEquals(2, count($article->photos));
        $this->assertEquals(false, $article->hasErrors());
        $this->assertEquals(true, $article->save());
        $this->assertNotNull($article->photos[0]->id);
        $article->refresh();
        $this->assertEquals($fileNamePrefix . ' test 1', $article->photos[0]->fileName);
        $this->assertEquals(2, Photo::find()->where(['like', 'fileName', $fileNamePrefix])->count());

        // One update, one insert, one delete
        $data['photos'] = [
            [
                'id' => $article->photos[0]->id,
                'fileName' => $fileNamePrefix . ' test 1 UPDATED',
            ],
            [
                'fileName' => $fileNamePrefix . ' test 3',
            ],
        ];
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals(true, $article->isRelationPopulated('photos'));
        $this->assertEquals(true, $article->save());
        $this->assertEquals($fileNamePrefix . ' test 1 UPDATED', $article->photos[0]->fileName);
        $this->assertEquals($fileNamePrefix . ' test 3', $article->photos[1]->fileName);
        $article->refresh();
        $this->assertEquals($fileNamePrefix . ' test 1 UPDATED', $article->photos[0]->fileName);
        $this->assertEquals($fileNamePrefix . ' test 3', $article->photos[1]->fileName);
        $this->assertEquals(2, Photo::find()->where(['like', 'fileName', $fileNamePrefix])->count());

        // Delete
        $data['photos'] = null;
        $this->assertEquals(true, $article->load($data, ''));
        $this->assertEquals(true, $article->save());
        $article->refresh();
        $this->assertEquals([], $article->photos);
        $this->assertEquals(0, Photo::find()->where(['like', 'fileName', $fileNamePrefix])->count());
    }

    public function testMultilevelRelationData()
    {
        $category = new Category();
        $category->listenRelationData('articles.photos.bar');
        $category->listenRelationData('articles.photos.foo');
        $category->listenRelationData('articles.attachments');
        $prefix = 'multi-level' . microtime(true);

        // Insert
        $data = [
            'title' => 'test category',
            'articles' => [
                [
                    'title' => $prefix . ' test article 1',
                    'attachments' => [
                        [
                            'fileName' => $prefix . ' attachment 1',
                        ],
                        [
                            'fileName' => $prefix . ' attachment 2',
                        ],
                    ],
                    'photos' => [
                        [
                            'fileName' => $prefix . ' photo 1',
                            'bar' => [
                                'name' => $prefix . ' bar 1'
                            ],
                            'foo' => [
                                'name' => $prefix . ' foo 1'
                            ],
                        ],
                        [
                            'fileName' => $prefix . ' photo 2',
                        ],
                    ],
                ],
                [
                    'title' => $prefix . ' test article 2',
                    'photos' => [
                        [
                            'fileName' => $prefix . ' photo 3',
                            'foo' => [
                                'name' => $prefix . ' foo 2'
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals(true, $category->load($data, ''));
        $this->assertEquals(true, $category->isRelationPopulated('articles'));
        $this->assertEquals(2, count($category->articles));
        $this->assertEquals(false, $category->hasErrors());

        // Check filled before save
        $this->assertEquals(true, $category->articles[0]->isRelationPopulated('attachments'));
        $this->assertEquals(true, $category->articles[0]->isRelationPopulated('photos'));
        $this->assertEquals(true, $category->articles[1]->isRelationPopulated('photos'));
        $this->assertEquals($prefix . ' test article 1', $category->articles[0]->title);
        $this->assertEquals($prefix . ' attachment 1', $category->articles[0]->attachments[0]->fileName);
        $this->assertEquals($prefix . ' attachment 2', $category->articles[0]->attachments[1]->fileName);
        $this->assertEquals($prefix . ' photo 1', $category->articles[0]->photos[0]->fileName);
        $this->assertEquals($prefix . ' bar 1', $category->articles[0]->photos[0]->bar->name);
        $this->assertEquals($prefix . ' foo 1', $category->articles[0]->photos[0]->foo->name);
        $this->assertEquals($prefix . ' photo 2', $category->articles[0]->photos[1]->fileName);
        $this->assertEquals($prefix . ' test article 2', $category->articles[1]->title);
        $this->assertEquals($prefix . ' photo 3', $category->articles[1]->photos[0]->fileName);
        $this->assertEquals($prefix . ' foo 2', $category->articles[1]->photos[0]->foo->name);

        $this->assertEquals(true, $category->save());
        $category->refresh();

        // Check fetch after save
        $this->assertEquals(2, count($category->articles[0]->attachments));
        $this->assertEquals(2, count($category->articles[0]->photos));
        $this->assertEquals(1, count($category->articles[1]->photos));
        $this->assertEquals($prefix . ' test article 1', $category->articles[0]->title);
        $this->assertEquals($prefix . ' attachment 1', $category->articles[0]->attachments[0]->fileName);
        $this->assertEquals($prefix . ' attachment 2', $category->articles[0]->attachments[1]->fileName);
        $this->assertEquals($prefix . ' photo 1', $category->articles[0]->photos[0]->fileName);
        $this->assertEquals($prefix . ' bar 1', $category->articles[0]->photos[0]->bar->name);
        $this->assertEquals($prefix . ' foo 1', $category->articles[0]->photos[0]->foo->name);
        $this->assertEquals($prefix . ' photo 2', $category->articles[0]->photos[1]->fileName);
        $this->assertEquals($prefix . ' test article 2', $category->articles[1]->title);
        $this->assertEquals($prefix . ' photo 3', $category->articles[1]->photos[0]->fileName);
        $this->assertEquals($prefix . ' foo 2', $category->articles[1]->photos[0]->foo->name);

        // Set pks
        $data['id'] = $category->primaryKey;
        $data['articles'][0]['id'] = $category->articles[0]->primaryKey;
        $data['articles'][0]['attachments'][0]['id'] = $category->articles[0]->attachments[0]->primaryKey;
        $data['articles'][0]['attachments'][0]['articleId'] = $category->articles[0]->primaryKey;
        $data['articles'][0]['attachments'][1]['id'] = $category->articles[0]->attachments[1]->primaryKey;
        $data['articles'][0]['attachments'][1]['articleId'] = $category->articles[0]->primaryKey;
        $data['articles'][0]['photos'][0]['id'] = $category->articles[0]->photos[0]->primaryKey;
        $data['articles'][0]['photos'][0]['barId'] = $category->articles[0]->photos[0]->bar->primaryKey;
        $data['articles'][0]['photos'][0]['foo']['photoId'] = $category->articles[0]->photos[0]->primaryKey;
        $data['articles'][0]['photos'][1]['id'] = $category->articles[0]->photos[1]->primaryKey;
        $data['articles'][1]['id'] = $category->articles[1]->primaryKey;
        $data['articles'][1]['photos'][0]['id'] = $category->articles[1]->photos[0]->primaryKey;
        $data['articles'][1]['photos'][0]['foo']['photoId'] = $category->articles[1]->photos[0]->primaryKey;

        // Check model created counts
        $this->assertEquals(2, Article::find()->where(['like', 'title', $prefix])->count());
        $this->assertEquals(2, Attachment::find()->where(['like', 'fileName', $prefix])->count());
        $this->assertEquals(3, Photo::find()->where(['like', 'fileName', $prefix])->count());
        $this->assertEquals(2, PhotoFoo::find()->where(['like', 'name', $prefix])->count());
        $this->assertEquals(1, PhotoBar::find()->where(['like', 'name', $prefix])->count());

        // Save
        $this->assertEquals(true, $category->load($data, ''));
        $this->assertEquals(true, $category->save());
        $category->refresh();

        // Check no added
        $this->assertEquals(2, Article::find()->where(['like', 'title', $prefix])->count());
        $this->assertEquals(2, Attachment::find()->where(['like', 'fileName', $prefix])->count());
        $this->assertEquals(3, Photo::find()->where(['like', 'fileName', $prefix])->count());
        $this->assertEquals(2, PhotoFoo::find()->where(['like', 'name', $prefix])->count());
        $this->assertEquals(1, PhotoBar::find()->where(['like', 'name', $prefix])->count());

        // One update, one insert, one delete
        $data['articles'][0]['attachments'][0]['fileName'] = $prefix . ' attachment UPDATED';
        $data['articles'][0]['photos'][] = [
            'fileName' => $prefix . ' photo 4',
        ];
        unset($data['articles'][0]['attachments'][1]);

        // Save
        $this->assertEquals(true, $category->load($data, ''));
        $this->assertEquals(true, $category->save());
        $category->refresh();

        $this->assertEquals($prefix . ' attachment UPDATED', $category->articles[0]->attachments[0]->fileName);
        $this->assertEquals($prefix . ' photo 4', $category->articles[0]->photos[2]->fileName);
        $this->assertEquals(1, Attachment::find()->where(['like', 'fileName', $prefix])->count());
        $this->assertEquals(4, Photo::find()->where(['like', 'fileName', $prefix])->count());
    }

    public function testMultilevelRelationIds()
    {
        $category = new Category();
        $category->listenRelationIds('articles.photos');
        $prefix = 'multi-level' . microtime(true);

        // Insert
        $data = [
            'title' => 'test category',
            'articles' => [
                [
                    'title' => $prefix . ' test article 1',
                    'photosIds' => [
                        $this->createPhoto($prefix . ' photo 1')->primaryKey,
                        $this->createPhoto($prefix . ' photo 2')->primaryKey,
                    ],
                ],
            ],
        ];

        $this->assertEquals(true, $category->load($data, ''));
        $this->assertEquals(true, $category->isRelationPopulated('articles'));
        $this->assertEquals(1, count($category->articles));
        $this->assertEquals(false, $category->hasErrors());

        $this->assertEquals(true, $category->save());
        $category->refresh();

        // Check fetch after save
        $this->assertEquals(2, count($category->articles[0]->photos));
        $this->assertEquals($prefix . ' test article 1', $category->articles[0]->title);
        $this->assertEquals($prefix . ' photo 1', $category->articles[0]->photos[0]->fileName);
        $this->assertEquals($prefix . ' photo 2', $category->articles[0]->photos[1]->fileName);

        // Delete
        $data['articles'][0]['id'] = $category->articles[0]->primaryKey;
        unset($data['articles'][0]['photosIds'][0]);

        $this->assertEquals(true, $category->load($data, ''));
        $this->assertEquals(1, count($category->articles[0]->photosIds));
        $this->assertEquals(true, $category->save());
        $category->refresh();

        $this->assertEquals(1, count($category->articles[0]->photos));
        $this->assertEquals($prefix . ' photo 2', $category->articles[0]->photos[0]->fileName);
    }

    public function testRelationErrors()
    {
        $category = new Category();
        $category->listenRelationData('articles.photos.bar');
        $category->listenRelationData('articles.photos.foo');

        // Insert
        $data = [
            'title' => new \stdClass(),
            'articles' => [
                [
                    'title' => [''],
                    'photos' => [
                        [
                            'fileName' => [''],
                            'bar' => [
                                'name' => ['']
                            ],
                            'foo' => [
                                'name' => ['']
                            ],
                        ],
                        [
                            'fileName' => [''],
                        ],
                    ],
                ],
            ],
        ];

        \Yii::$app->language = 'en';
        $this->assertEquals(true, $category->load($data, ''));
        $this->assertEquals(false, $category->validate());
        $this->assertEquals([
            'title' => [
                'Title must be a string.',
            ],
            'articles' => [
                [
                    'title' => [
                        'Title must be a string.',
                    ],
                    'photos' => [
                        [
                            'fileName' => [
                                'File Name must be a string.',
                            ],
                            'bar' => [
                                'name' => [
                                    'Name must be a string.',
                                ]
                            ],
                            'foo' => [
                                'name' => [
                                    'Name must be a string.',
                                ]
                            ],
                        ],
                        [
                            'fileName' => [
                                'File Name must be a string.'
                            ],
                        ],
                    ],
                ],
            ],
        ], $category->getErrors());
    }

    protected function createPhoto($fileName = null)
    {
        $photo = new Photo([
            'fileName' => $fileName ?? (string) microtime(true),
        ]);
        $photo->saveOrPanic();
        return $photo;
    }

    protected function createAttachment()
    {
        $attachment = new Attachment([
            'fileName' => microtime(true),
        ]);
        $attachment->saveOrPanic();
        return $attachment;
    }
}
