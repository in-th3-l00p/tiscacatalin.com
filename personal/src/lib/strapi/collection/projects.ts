import strapi, {prefixUrl, StrapiImage} from "@/lib/strapi";

interface StrapiProject  {
  title: string;
  description: string;
  urlName: string;
  url: string;
  icon: StrapiImage;
}

export interface Project {
  name: string;
  description: string;
  link: {
    href: string;
    label: string;
  };
  icon: StrapiImage;
}

export default async function getAllProjects() {
  const strapiProjects = (await strapi.find<StrapiProject[]>("projects", {
    sort: ["priority:desc", "createdAt:asc"],
    populate: "*"
  })).data;
  return strapiProjects.map(project => ({
    name: project.title,
    description: project.description,
    link: {
      href: project.url,
      label: project.urlName
    },
    icon: prefixUrl(project.icon)
  }));
}
