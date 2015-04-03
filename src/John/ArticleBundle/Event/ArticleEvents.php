<?php
namespace John\ArticleBundle\Event;

final class ArticleEvents
{

    /**
     * - Article.activate is called every time the article is activated by admin.
     * - Listener will receive an instance of John/ArticleBundle/Event/ArticleEvent
     * - Images from article's body are transformed  for PicturesFill
     * - The owner of the article receove an email after activation
     *
     */

   const ARTICLE_ACTIVATE = "article.activate";
}