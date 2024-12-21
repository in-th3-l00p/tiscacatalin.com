import ArticleLayout from "@/components/layout/articleLayout";
import {getArticle} from "@/lib/strapi/collection/articles";
import {BlocksRenderer} from "@strapi/blocks-react-renderer";
import "./style.css";

export default async function article({ params }: { params: {  slug: string } }) {
  const article = await getArticle(params.slug);

  return (
    <ArticleLayout article={article}>
      <BlocksRenderer 
	      content={article.content} 
      />
    </ArticleLayout>
  );
}
