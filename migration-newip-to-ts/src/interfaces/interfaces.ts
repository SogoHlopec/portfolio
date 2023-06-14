interface ISources {
    id: string;
    name: string;
    description: string;
    url: string;
    category: string;
    language: string;
    country: string;
}

interface IResp {
    status: string;
    sources: ISources[];
}

interface ISource {
    id: string;
    name: string;
}

interface IArticle {
    source: ISource;
    author: string;
    content: string;
    description: string;
    publishedAt: string;
    title: string;
    url: string;
    urlToImage: string;
}

interface INews {
    articles: IArticle[];
    status: string;
    totalResults: number;
}

interface IOptions {
    sources?: string;
    apiKey?: string;
}

enum OptionsFields {
    sources = 'sources',
    apiKey = 'apiKey',
}

export { ISources, IResp, ISource, IArticle, INews, IOptions, OptionsFields };
