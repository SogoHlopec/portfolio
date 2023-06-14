import { Source } from '../../../interfaces/index';
import './sources.css';

class Sources {
    draw(data: Source[]) {
        const fragment = document.createDocumentFragment();
        const sourceItemTemp: HTMLTemplateElement | null = document.querySelector('#sourceItemTemp');

        data.forEach((item) => {
            if (sourceItemTemp) {
                const sourceClone = sourceItemTemp.content.cloneNode(true) as HTMLTemplateElement;

                (sourceClone.querySelector('.source__item-name') as HTMLTemplateElement).textContent = item.name;
                (sourceClone.querySelector('.source__item') as HTMLTemplateElement).setAttribute(
                    'data-source-id',
                    item.id
                );

                fragment.append(sourceClone);
            }
        });

        (document.querySelector('.sources') as HTMLTemplateElement).append(fragment);
    }
}

export default Sources;
