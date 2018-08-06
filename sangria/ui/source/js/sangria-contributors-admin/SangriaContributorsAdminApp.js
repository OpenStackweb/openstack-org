import React from 'react';
import { connect } from 'react-redux';
import { fetchAll, fetchReleases, ingestContributors, exportContributors } from './actions';
import { Pagination } from 'react-bootstrap';
import { AjaxLoader, Table, Dropdown } from 'openstack-uicore-foundation/lib/components';
import 'react-select/dist/react-select.css';
import Select from 'react-select';

class SangriaContributorsAdminApp extends React.Component
{
    constructor(props) {
        super(props);

        this.handleExport        = this.handleExport.bind(this);
        this.handleIngest        = this.handleIngest.bind(this);
        this.handleReleaseChange = this.handleReleaseChange.bind(this);
        this.handlePageChange    = this.handlePageChange.bind(this);
        this.handleSort          = this.handleSort.bind(this);
    }

    componentDidMount() {
        let {items, page, order, orderDir, selectedReleases} = this.props;

        if(!items.length) {
            this.props.fetchAll(order, orderDir, page, selectedReleases);
        }

        this.props.fetchReleases();
    }

    handleExport() {
        let {order, orderDir, selectedReleases} = this.props;
        this.props.exportContributors(order, orderDir, selectedReleases);

    };

    handleIngest(ev) {
        let files = ev.target.files;
        let formData = new FormData();

        for (var i in files) {
            let file = files[i];
            formData.append('file[]', file);
        }

        this.props.ingestContributors(formData);
    };

    handleReleaseChange(value) {
        let {order, orderDir, page} = this.props;

        this.props.fetchAll(order, orderDir, page, value);
    };

    handlePageChange(page) {
        let {order, orderDir, selectedReleases} = this.props;
        this.props.fetchAll(order, orderDir, page, selectedReleases);
    }

    handleSort(index, key, dir, func) {
        let {page, selectedReleases} = this.props;
        this.props.fetchAll(key, dir, page, selectedReleases);
    }


    render() {
        let {items, order, orderDir, lastPage, page, selectedReleases, allReleases, totalItems} = this.props;

        let releases_ddl = allReleases.map(r => ({label: r.Name, value: r.ID}));

        let columns = [
            { columnKey: 'release', value: 'Release', sortable: true },
            { columnKey: 'first_name', value: 'First Name', sortable: true },
            { columnKey: 'last_name', value: 'Last Name', sortable: true },
            { columnKey: 'email', value: 'Email' },
            { columnKey: 'commit_count', value: 'Commits' },
            { columnKey: 'first_commit', value: 'First' },
            { columnKey: 'last_commit', value: 'Last' },
            { columnKey: 'city', value: 'City' },
            { columnKey: 'state', value: 'State' },
            { columnKey: 'country', value: 'Country' }
        ];

        let table_options = {
            className: "dataTable",
            sortCol: order,
            sortDir: orderDir
        }

        return (
            <div>
                <AjaxLoader show={this.props.loading} size={ 75 } />

                <h2>Release Cycle Contributors Admin (total: {totalItems})</h2>

                <div className="row">
                    <div className="col-md-4 form-inline">
                        <Select
                            id="releases"
                            className="right-space"
                            value={selectedReleases}
                            onChange={this.handleReleaseChange}
                            options={releases_ddl}
                            placeholder="Select Releases"
                            multi={true}
                        />
                    </div>
                    <div className="col-md-2">
                        <button className="btn btn-default" onClick={this.handleExport}>Export</button>
                    </div>
                    <div className="col-md-4">
                        <button className="btn btn-primary" onClick={this.handleIngest}>Ingest</button>
                    </div>
                </div>

                {items.length > 0 &&
                <div>
                    <Table
                        options={table_options}
                        data={items}
                        columns={columns}
                        onSort={this.handleSort}
                        className="dataTable"
                    />
                    <Pagination
                        bsSize="medium"
                        prev
                        next
                        first
                        last
                        ellipsis
                        boundaryLinks
                        maxButtons={10}
                        items={lastPage}
                        activePage={page}
                        onSelect={this.handlePageChange}
                    />
                </div>
                }

                {items.length == 0 &&
                    <p className="no-contributors"> No contributions found </p>
                }
            </div>
        );
    }
}

const mapStateToProps = (state) => ({
    ...state
})

export default connect (
    mapStateToProps,
    {
        fetchAll,
        fetchReleases,
        ingestContributors,
        exportContributors
    }
)(SangriaContributorsAdminApp);
