import React from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';

import {fetchMeta} from '../../actions/fields';
import {isMetaFetched} from '../../reducers/fields';

export default (names = []) => WrappedComponent => @connect(
    state => ({
        isMetaFetched: isMetaFetched(state),
    })
)
    class MetaFetcherHoc extends React.PureComponent {

    static WrappedComponent = WrappedComponent;

    /**
     * Proxy real name, prop types and default props for storybook
     */
    static displayName = WrappedComponent.displayName || WrappedComponent.name;

    static propTypes = {
        ...WrappedComponent.propTypes,
        isMetaFetched: PropTypes.bool,
    };

    static defaultProps = {
        ...WrappedComponent.defaultProps,
        isMetaFetched: false,
    };

    componentWillMount() {
        this.props.dispatch(fetchMeta(names));
    }

    render() {
        return (
            <WrappedComponent {...this.props}/>
        );
    }
};
