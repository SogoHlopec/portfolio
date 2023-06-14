import { ISource } from '../../../interfaces/interfaces';
import './sources.css';

class Sources {
    draw(data: ISource[]) {
        const fragment: DocumentFragment = document.createDocumentFragment();
        const sourceItemTemp: HTMLTemplateElement | null = document.querySelector('#sourceItemTemp');

        data.forEach((item: ISource) => {
            if (sourceItemTemp) {
                const sourceClone = sourceItemTemp.content.cloneNode(true);
                if (sourceClone instanceof DocumentFragment) {
                    const sourceItemName = sourceClone.querySelector('.source__item-name');
                    if (sourceItemName) sourceItemName.textContent = item.name;

                    const sourceItem = sourceClone.querySelector('.source__item');
                    sourceItem?.setAttribute('data-source-id', item.id);

                    fragment.append(sourceClone);
                }
            }
        });

        (document.querySelector('.sources') as HTMLTemplateElement).append(fragment);
    }
}

export default Sources;
