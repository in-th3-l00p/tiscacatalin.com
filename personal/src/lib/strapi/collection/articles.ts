import glob from 'fast-glob'
import strapi from "@/lib/strapi";
import {BlocksContent} from "@strapi/blocks-react-renderer";
import {notFound} from "next/navigation";

interface StrapiArticle {
  id: string;
  title: string;
  slug: string;
  description: string;
  createdAt: string;
}

interface StrapiContentArticle extends StrapiArticle {
  content: BlocksContent
}

export interface Article {
  id: string;
  title: string;
  slug: string;
  description: string;
  date: string;
}

export interface ContentArticle extends Article {
  content: BlocksContent;
}

export async function getAllArticles() {
  const articles: Article[] = (await strapi.find<StrapiArticle[]>(
    "articles",
    {
      sort: "createdAt:desc",
      fields: [ "id", "title", "slug", "description", "createdAt" ]
    }
  ))
    .data
    .map(article => ({
      id: article.id,
      title: article.title,
      slug: article.slug,
      description: article.description,
      date: article.createdAt.substring(0, 10)
    }));
  return articles;
}

export async function getArticle(slug: string) {
  const strapiArticles = (await strapi.find<StrapiContentArticle[]>("articles", {
    filters: {
      slug: {
        $eq: slug
      }
    }
  })).data;
  if (strapiArticles.length === 0)
    return notFound();
  const strapiArticle = strapiArticles[0];
  return {
    id: strapiArticle.id,
    title: strapiArticle.title,
    slug: strapiArticle.slug,
    description: strapiArticle.description,
    date: strapiArticle.createdAt.substring(0, 10),
    content: strapiArticle.content
  }
}
