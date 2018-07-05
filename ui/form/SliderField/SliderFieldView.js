import React from 'react';
import PropTypes from 'prop-types';
import Slider from 'rc-slider';

import {html} from 'components';

const bem = html.bem('SliderFieldView');

export default class SliderFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        disabled: PropTypes.bool,
        className: PropTypes.string,
        slider: PropTypes.object,
        min: PropTypes.number,
        max: PropTypes.number,
        onSlide: PropTypes.func,
        onSlideEnd: PropTypes.func,
    };

    render() {
        return (
            <div className={bem(
                bem.block({
                    size: this.props.size,
                }),
                this.props.className,
            )}>
                <Slider
                    {...this.props.slider}
                    className={bem.element('slider')}
                />
            </div>
        );
    }

}
