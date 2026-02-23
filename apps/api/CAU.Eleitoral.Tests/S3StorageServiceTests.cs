using Amazon.S3;
using Amazon.S3.Model;
using CAU.Eleitoral.Infrastructure.Services.Storage;
using FluentAssertions;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.Logging;
using Moq;

namespace CAU.Eleitoral.Tests;

public class S3StorageServiceTests
{
    private readonly Mock<IAmazonS3> _s3Mock;
    private readonly Mock<ILogger<S3StorageService>> _loggerMock;
    private readonly S3StorageService _sut;

    public S3StorageServiceTests()
    {
        _s3Mock = new Mock<IAmazonS3>();
        _loggerMock = new Mock<ILogger<S3StorageService>>();

        var config = new ConfigurationBuilder()
            .AddInMemoryCollection(new Dictionary<string, string?>
            {
                ["AWS:S3:BucketDocuments"] = "test-bucket"
            })
            .Build();

        _sut = new S3StorageService(_s3Mock.Object, config, _loggerMock.Object);
    }

    [Fact]
    public async Task UploadAsync_CallsS3WithCorrectParameters()
    {
        _s3Mock.Setup(s => s.PutObjectAsync(It.IsAny<PutObjectRequest>(), It.IsAny<CancellationToken>()))
            .ReturnsAsync(new PutObjectResponse());

        using var stream = new MemoryStream(new byte[] { 1, 2, 3 });

        var result = await _sut.UploadAsync(stream, "docs/test.pdf", "application/pdf");

        result.Should().Be("s3://test-bucket/docs/test.pdf");

        _s3Mock.Verify(s => s.PutObjectAsync(
            It.Is<PutObjectRequest>(r =>
                r.BucketName == "test-bucket" &&
                r.Key == "docs/test.pdf" &&
                r.ContentType == "application/pdf" &&
                r.ServerSideEncryptionMethod == ServerSideEncryptionMethod.AES256),
            It.IsAny<CancellationToken>()),
            Times.Once);
    }

    [Fact]
    public async Task UploadAsync_ReturnsS3Uri()
    {
        _s3Mock.Setup(s => s.PutObjectAsync(It.IsAny<PutObjectRequest>(), It.IsAny<CancellationToken>()))
            .ReturnsAsync(new PutObjectResponse());

        using var stream = new MemoryStream(new byte[] { 1 });
        var result = await _sut.UploadAsync(stream, "documentos/abc/file.pdf", "application/pdf");

        result.Should().StartWith("s3://test-bucket/");
        result.Should().Contain("documentos/abc/file.pdf");
    }

    [Fact]
    public async Task DownloadAsync_ReturnsStreamFromS3()
    {
        var expectedData = new byte[] { 10, 20, 30, 40, 50 };
        var responseStream = new MemoryStream(expectedData);

        _s3Mock.Setup(s => s.GetObjectAsync("test-bucket", "docs/test.pdf", It.IsAny<CancellationToken>()))
            .ReturnsAsync(new GetObjectResponse { ResponseStream = responseStream });

        var result = await _sut.DownloadAsync("docs/test.pdf");

        result.Should().NotBeNull();
        using var reader = new MemoryStream();
        await result.CopyToAsync(reader);
        reader.ToArray().Should().BeEquivalentTo(expectedData);
    }

    [Fact]
    public async Task DeleteAsync_CallsS3DeleteObject()
    {
        _s3Mock.Setup(s => s.DeleteObjectAsync("test-bucket", "docs/old.pdf", It.IsAny<CancellationToken>()))
            .ReturnsAsync(new DeleteObjectResponse());

        await _sut.DeleteAsync("docs/old.pdf");

        _s3Mock.Verify(s => s.DeleteObjectAsync("test-bucket", "docs/old.pdf", It.IsAny<CancellationToken>()),
            Times.Once);
    }

    [Fact]
    public async Task GetPresignedUrlAsync_ReturnsUrl()
    {
        _s3Mock.Setup(s => s.GetPreSignedURL(It.IsAny<GetPreSignedUrlRequest>()))
            .Returns("https://test-bucket.s3.amazonaws.com/docs/test.pdf?X-Amz-Signature=abc");

        var result = await _sut.GetPresignedUrlAsync("docs/test.pdf", 30);

        result.Should().Contain("test-bucket");
        result.Should().Contain("docs/test.pdf");

        _s3Mock.Verify(s => s.GetPreSignedURL(
            It.Is<GetPreSignedUrlRequest>(r =>
                r.BucketName == "test-bucket" &&
                r.Key == "docs/test.pdf")),
            Times.Once);
    }

    [Fact]
    public async Task Constructor_WithDefaultBucket_UsesConfigValue()
    {
        var config = new ConfigurationBuilder()
            .AddInMemoryCollection(new Dictionary<string, string?>
            {
                ["AWS:S3:BucketDocuments"] = "custom-bucket"
            })
            .Build();

        var sut = new S3StorageService(_s3Mock.Object, config, _loggerMock.Object);

        // Verify by uploading and checking the bucket
        _s3Mock.Setup(s => s.PutObjectAsync(It.IsAny<PutObjectRequest>(), It.IsAny<CancellationToken>()))
            .ReturnsAsync(new PutObjectResponse());

        using var stream = new MemoryStream(new byte[] { 1 });
        var result = await sut.UploadAsync(stream, "test.pdf", "application/pdf");

        result.Should().Be("s3://custom-bucket/test.pdf");
    }

    [Fact]
    public async Task Constructor_WithMissingConfig_DefaultsToCauBucket()
    {
        var config = new ConfigurationBuilder()
            .AddInMemoryCollection(new Dictionary<string, string?>())
            .Build();

        var sut = new S3StorageService(_s3Mock.Object, config, _loggerMock.Object);

        _s3Mock.Setup(s => s.PutObjectAsync(It.IsAny<PutObjectRequest>(), It.IsAny<CancellationToken>()))
            .ReturnsAsync(new PutObjectResponse());

        using var stream = new MemoryStream(new byte[] { 1 });
        var result = await sut.UploadAsync(stream, "test.pdf", "application/pdf");

        result.Should().Be("s3://cau-eleitoral-documents/test.pdf");
    }
}
