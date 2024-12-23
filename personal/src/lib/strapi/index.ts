import Strapi from "strapi-sdk-js";

const strapi = new Strapi({
  url: process.env.STRAPI_URL || "http://localhost:1337"
});

export interface Link {
  type: string;
  label: string;
  url: string;
}

export interface StrapiMedia {
  name: string;
  url: string;
}

export interface StrapiImage extends StrapiMedia {
  alternativeText: string;
}

export function prefixUrl<T extends StrapiMedia>(media: T) {
  media.url = strapi.options.url + media.url;
  return media;
}

export default strapi;
